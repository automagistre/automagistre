alter table "public"."car"
  add constraint "car_vehicle_id_fkey"
  foreign key ("vehicle_id")
  references "public"."vehicle"
  ("id") on update restrict on delete restrict;
