alter table "public"."car"
  add constraint "car_tenant_group_id_fkey"
  foreign key ("tenant_group_id")
  references "public"."tenant_group"
  ("id") on update restrict on delete restrict;
