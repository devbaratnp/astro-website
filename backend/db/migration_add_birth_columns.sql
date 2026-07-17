ALTER TABLE appointments
  ADD COLUMN nwaran_name VARCHAR(100) DEFAULT '' AFTER birth_place,
  ADD COLUMN father_name VARCHAR(100) DEFAULT '' AFTER nwaran_name,
  ADD COLUMN mother_name VARCHAR(100) DEFAULT '' AFTER father_name,
  ADD COLUMN birth_order VARCHAR(20) DEFAULT '' AFTER mother_name,
  ADD COLUMN birth_gender VARCHAR(10) DEFAULT '' AFTER birth_order;
