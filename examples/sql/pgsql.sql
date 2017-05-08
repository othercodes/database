CREATE TABLE ts_users (
  id serial primary key,
  name char(20) DEFAULT NULL,
  surname char(30) DEFAULT NULL
);

INSERT INTO ts_users VALUES (1,'Walter','White');
INSERT INTO ts_users VALUES (2,'Sheldon','Cooper');