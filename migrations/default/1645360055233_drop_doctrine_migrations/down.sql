CREATE TABLE public.migration_versions
    (
        version character varying(1024) NOT NULL,
        executed_at timestamp(0) WITHOUT TIME ZONE DEFAULT NULL::timestamp WITHOUT TIME ZONE,
        execution_time integer
    );
