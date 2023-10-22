CREATE USER 'admin'@'%' IDENTIFIED BY '';
CREATE DATABASE IF NOT EXISTS main_dev;
GRANT ALL ON main_dev.* TO 'admin'@'%';
CREATE DATABASE IF NOT EXISTS main_test;
GRANT ALL ON main_test.* TO 'admin'@'%';
FLUSH PRIVILEGES;
