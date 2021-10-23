-- This is based on 2ndQuadrant/audit-trigger.
--
-- Few changes from the original
-- 1. Requires postgres >= 10
-- 2. Row data is stored in jsonb

-- The following are comments preserved from the original file:

--> -- An audit history is important on most tables. Provide an audit trigger that logs to
--> -- a dedicated audit table for the major relations.
--> --
--> -- This file should be generic and not depend on application roles or structures,
--> -- as it's being listed here:
--> --
--> -- This trigger was originally based on
--> --   http://wiki.postgresql.org/wiki/Audit_trigger
--> -- but has been completely rewritten.
--> --

CREATE SCHEMA audit;
REVOKE ALL ON SCHEMA audit FROM PUBLIC;

COMMENT ON SCHEMA audit IS 'Out-of-table audit/history logging tables and trigger functions';

--
-- Audited data. Lots of information is available, it's just a matter of how much
-- you really want to record. See:
--
--   http://www.postgresql.org/docs/9.1/static/functions-info.html
--
-- Remember, every column you add takes up more audit table space and slows audit
-- inserts.
--
-- Every index you add has a big impact too, so avoid adding indexes to the
-- audit table unless you REALLY need them.
--
-- It is sometimes worth copying the audit table, or a coarse subset of it that
-- you're interested in, into a temporary table where you CREATE any useful
-- indexes and do your analysis.
--
CREATE TABLE audit.logged_actions
(
    event_id          bigserial PRIMARY KEY,

    schema_name       text                     NOT NULL,
    table_name        text                     NOT NULL,
    relid             oid                      NOT NULL,

    session_user_name text,
    hasura_user       jsonb,

    action_tstamp_tx  TIMESTAMP WITH TIME ZONE NOT NULL,
    action_tstamp_stm TIMESTAMP WITH TIME ZONE NOT NULL,
    action_tstamp_clk TIMESTAMP WITH TIME ZONE NOT NULL,
    transaction_id    bigint,

    application_name  text,
    client_addr       inet,
    client_port       integer,

    client_query      text,
    action            TEXT                     NOT NULL CHECK (action IN ('I', 'D', 'U', 'T')),
    row_data          jsonb,
    changed_fields    jsonb,
    statement_only    boolean                  NOT NULL
);

REVOKE ALL ON audit.logged_actions FROM PUBLIC;

COMMENT ON TABLE audit.logged_actions IS 'History of auditable actions on audited tables, from audit.if_modified_func()';
COMMENT ON COLUMN audit.logged_actions.event_id IS 'Unique identifier for each auditable event';
COMMENT ON COLUMN audit.logged_actions.schema_name IS 'Database schema audited table for this event is in';
COMMENT ON COLUMN audit.logged_actions.table_name IS 'Non-schema-qualified table name of table event occured in';
COMMENT ON COLUMN audit.logged_actions.relid IS 'Table OID. Changes with drop/create. Get with ''tablename''::regclass';
COMMENT ON COLUMN audit.logged_actions.session_user_name IS 'Login / session user whose statement caused the audited event';
COMMENT ON COLUMN audit.logged_actions.action_tstamp_tx IS 'Transaction start timestamp for tx in which audited event occurred';
COMMENT ON COLUMN audit.logged_actions.action_tstamp_stm IS 'Statement start timestamp for tx in which audited event occurred';
COMMENT ON COLUMN audit.logged_actions.action_tstamp_clk IS 'Wall clock time at which audited event''s trigger call occurred';
COMMENT ON COLUMN audit.logged_actions.transaction_id IS 'Identifier of transaction that made the change. May wrap, but unique paired with action_tstamp_tx.';
COMMENT ON COLUMN audit.logged_actions.client_addr IS 'IP address of client that issued query. Null for unix domain socket.';
COMMENT ON COLUMN audit.logged_actions.client_port IS 'Remote peer IP port address of client that issued query. Undefined for unix socket.';
COMMENT ON COLUMN audit.logged_actions.client_query IS 'Top-level query that caused this auditable event. May be more than one statement.';
COMMENT ON COLUMN audit.logged_actions.application_name IS 'Application name set when this audit event occurred. Can be changed in-session by client.';
COMMENT ON COLUMN audit.logged_actions.action IS 'Action type; I = insert, D = delete, U = update, T = truncate';
COMMENT ON COLUMN audit.logged_actions.row_data IS 'Record value. Null for statement-level trigger. For INSERT this is the new tuple. For DELETE and UPDATE it is the old tuple.';
COMMENT ON COLUMN audit.logged_actions.changed_fields IS 'New values of fields changed by UPDATE. Null except for row-level UPDATE events.';
COMMENT ON COLUMN audit.logged_actions.statement_only IS '''t'' if audit event is from an FOR EACH STATEMENT trigger, ''f'' for FOR EACH ROW';

CREATE INDEX logged_actions_relid_idx ON audit.logged_actions (relid);
CREATE INDEX logged_actions_action_tstamp_tx_stm_idx ON audit.logged_actions (action_tstamp_stm);
CREATE INDEX logged_actions_action_idx ON audit.logged_actions (action);

CREATE OR REPLACE FUNCTION audit.if_modified_func() RETURNS TRIGGER AS
$body$
DECLARE
    audit_row     audit.logged_actions;
    excluded_cols text[] = ARRAY []::text[];
    new_r         jsonb;
    old_r         jsonb;
BEGIN
    IF tg_when <> 'AFTER' THEN RAISE EXCEPTION 'audit.if_modified_func() may only run as an AFTER trigger'; END IF;

    audit_row = ROW (NEXTVAL('audit.logged_actions_event_id_seq'), -- event_id
        tg_table_schema::text, -- schema_name
        tg_table_name::text, -- table_name
        tg_relid, -- relation OID for much quicker searches
        SESSION_USER::text, -- session_user_name
        CURRENT_SETTING('hasura.user', 't')::jsonb, -- user information from hasura graphql engine
        CURRENT_TIMESTAMP, -- action_tstamp_tx
        STATEMENT_TIMESTAMP(), -- action_tstamp_stm
        CLOCK_TIMESTAMP(), -- action_tstamp_clk
        TXID_CURRENT(), -- transaction ID
        CURRENT_SETTING('application_name'), -- client application
        INET_CLIENT_ADDR(), -- client_addr
        INET_CLIENT_PORT(), -- client_port
        CURRENT_QUERY(), -- top-level query or queries (if multistatement) from client
        SUBSTRING(tg_op, 1, 1), -- action
        NULL, NULL, -- row_data, changed_fields
        'f' -- statement_only
        );

    IF NOT tg_argv[0]::boolean IS DISTINCT FROM 'f'::boolean THEN audit_row.client_query = NULL; END IF;

    IF tg_argv[1] IS NOT NULL THEN excluded_cols = tg_argv[1]::text[]; END IF;

    IF (tg_op = 'UPDATE' AND tg_level = 'ROW') THEN
        old_r = TO_JSONB(old);
        new_r = TO_JSONB(new);
        audit_row.row_data = old_r - excluded_cols;
        SELECT JSONB_OBJECT_AGG(new_t.key, new_t.value) - excluded_cols
        INTO audit_row.changed_fields
        FROM JSONB_EACH(old_r) AS old_t
                 JOIN JSONB_EACH(new_r) AS new_t ON (old_t.key = new_t.key AND old_t.value <> new_t.value);
    ELSIF (tg_op = 'DELETE' AND tg_level = 'ROW') THEN
        audit_row.row_data = TO_JSONB(old) - excluded_cols;
    ELSIF (tg_op = 'INSERT' AND tg_level = 'ROW') THEN
        audit_row.row_data = TO_JSONB(new) - excluded_cols;
    ELSIF (tg_level = 'STATEMENT' AND tg_op IN ('INSERT', 'UPDATE', 'DELETE', 'TRUNCATE')) THEN
        audit_row.statement_only = 't';
    ELSE
        RAISE EXCEPTION '[audit.if_modified_func] - Trigger func added as trigger for unhandled case: %, %',tg_op, tg_level;
        RETURN NULL;
    END IF;
    INSERT INTO audit.logged_actions VALUES (audit_row.*);
    RETURN NULL;
END;
$body$ LANGUAGE plpgsql SECURITY DEFINER
                        SET search_path = pg_catalog, public;


COMMENT ON FUNCTION audit.if_modified_func() IS $body$
Track changes to a table at the statement and/or row level.

Optional parameters to trigger in CREATE TRIGGER call:

param 0: boolean, whether to log the query text. Default 't'.

param 1: text[], columns to ignore in updates. Default [].

         Updates to ignored cols are omitted from changed_fields.

         Updates with only ignored cols changed are not inserted
         into the audit log.

         Almost all the processing work is still done for updates
         that ignored. If you need to save the load, you need to use
         WHEN clause on the trigger instead.

         No warning or error is issued if ignored_cols contains columns
         that do not exist in the target table. This lets you specify
         a standard set of ignored columns.

There is no parameter to disable logging of values. Add this trigger as
a 'FOR EACH STATEMENT' rather than 'FOR EACH ROW' trigger if you do not
want to log row values.

Note that the user name logged is the login role for the session. The audit trigger
cannot obtain the active role because it is reset by the SECURITY DEFINER invocation
of the audit trigger its self.
$body$;



CREATE OR REPLACE FUNCTION audit.audit_table(target_table regclass, audit_rows boolean, audit_query_text boolean,
                                             ignored_cols text[]) RETURNS void AS
$body$
DECLARE
    stm_targets        text = 'INSERT OR UPDATE OR DELETE OR TRUNCATE';
    _q_txt             text;
    _ignored_cols_snip text = '';
BEGIN
    EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_row ON ' || target_table;
    EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_stm ON ' || target_table;

    IF audit_rows THEN
        IF ARRAY_LENGTH(ignored_cols, 1) > 0 THEN _ignored_cols_snip = ', ' || QUOTE_LITERAL(ignored_cols); END IF;
        _q_txt = 'CREATE TRIGGER audit_trigger_row AFTER INSERT OR UPDATE OR DELETE ON ' || target_table ||
                 ' FOR EACH ROW EXECUTE PROCEDURE audit.if_modified_func(' || QUOTE_LITERAL(audit_query_text) ||
                 _ignored_cols_snip || ');';
        RAISE NOTICE '%',_q_txt;
        EXECUTE _q_txt;
        stm_targets = 'TRUNCATE';
    ELSE
    END IF;

    _q_txt = 'CREATE TRIGGER audit_trigger_stm AFTER ' || stm_targets || ' ON ' || target_table ||
             ' FOR EACH STATEMENT EXECUTE PROCEDURE audit.if_modified_func(' || QUOTE_LITERAL(audit_query_text) || ');';
    RAISE NOTICE '%',_q_txt;
    EXECUTE _q_txt;

END;
$body$ LANGUAGE 'plpgsql';

COMMENT ON FUNCTION audit.audit_table(regclass, boolean, boolean, text[]) IS $body$
Add auditing support to a table.

Arguments:
   target_table:     Table name, schema qualified if not on search_path
   audit_rows:       Record each row change, or only audit at a statement level
   audit_query_text: Record the text of the client query that triggered the audit event?
   ignored_cols:     Columns to exclude from update diffs, ignore updates that change only ignored cols.
$body$;

-- Pg doesn't allow variadic calls with 0 params, so provide a wrapper
CREATE OR REPLACE FUNCTION audit.audit_table(target_table regclass, audit_rows boolean, audit_query_text boolean) RETURNS void AS
$body$
SELECT audit.audit_table($1, $2, $3, ARRAY []::text[]);
$body$ LANGUAGE sql;

-- And provide a convenience call wrapper for the simplest case
-- of row-level logging with no excluded cols and query logging enabled.
--
CREATE OR REPLACE FUNCTION audit.audit_table(target_table regclass) RETURNS void AS
$body$
SELECT audit.audit_table($1, BOOLEAN 't', BOOLEAN 't');
$body$ LANGUAGE 'sql';

COMMENT ON FUNCTION audit.audit_table(regclass) IS $body$
Add auditing support to the given table. Row-level changes will be logged with full client query text. No cols are ignored.
$body$;
