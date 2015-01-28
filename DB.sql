DROP DATABASE IF EXISTS ilaw;

CREATE DATABASE IF NOT EXISTS ilaw;

USE ilaw;

DROP TABLE IF EXISTS bulb;

CREATE TABLE IF NOT EXISTS bulb (bulbid int(11) NOT NULL AUTO_INCREMENT, ipaddress varchar(15) DEFAULT NULL, streetadd varchar(100) DEFAULT NULL, latitude varchar(50) DEFAULT NULL, longitude varchar(50) DEFAULT NULL, state varchar(5) DEFAULT NULL, currbrightness int(3) DEFAULT NULL, mode varchar(10) DEFAULT NULL, name varchar(200) DEFAULT NULL, PRIMARY KEY (bulbid));

INSERT INTO bulb VALUES (1,'192.168.2.4','Roxas Avenue, Quezon City, Philippines','14.654724728461096','121.06472724547734','on',51,'control','Oblation Lamp'),(2,'192.168.2.5','University Avenue, Quezon City, Philippines','14.65395662152603','121.05417007079473','off',0,'control','Philcoa Lamp'),(3,'192.168.2.6','Maharlika, Quezon City, Philippines','14.650489726993586','121.05228179564824','cnbr', 0,'control','Maharlika Lamp');

DROP TABLE IF EXISTS cluster;

CREATE TABLE IF NOT EXISTS cluster (clusterid int(11) NOT NULL AUTO_INCREMENT, name varchar(100) DEFAULT NULL, PRIMARY KEY (clusterid));

INSERT INTO cluster VALUES (1,'Diliman QC Area'),(2,'Elliptical Road Area');

DROP TABLE IF EXISTS cluster_bulb;

CREATE TABLE IF NOT EXISTS cluster_bulb (bulbid int(11) NOT NULL, clusterid int(11) NOT NULL);

INSERT INTO cluster_bulb VALUES (1,1),(1,2),(2,2),(3,1),(3,2);

DROP TABLE IF EXISTS poweranalyzer;

CREATE TABLE poweranalyzer (bulbid int(11) DEFAULT NULL, stat varchar(45) DEFAULT NULL, watts varchar(45) DEFAULT NULL, va varchar(45) DEFAULT NULL, var_ varchar(45) DEFAULT NULL, pf varchar(45) DEFAULT NULL, volt varchar(45) DEFAULT NULL, ampere varchar(45) DEFAULT NULL, timestamp datetime DEFAULT NULL);

INSERT INTO poweranalyzer VALUES (1,'InRange','10.8765','0.098764','0.9878009','0.0987','0.0987','245.0','2013-11-13 02:25:41');
INSERT INTO poweranalyzer VALUES (2,'InRange','0.092244','0.098812','0.08871','0.09886','0.00987','100.0','2013-11-13 02:25:46');
INSERT INTO poweranalyzer VALUES (3,'InRange','0.00987','0.09485','0.12445','0.09847','0.088575','125.0','2013-11-13 02:27:00');
INSERT INTO poweranalyzer VALUES (1,'InRange','12.23423','0.2312','90.2213','12.3123','342.12993','993.21230','2013-11-13 02:25:41');

DROP TABLE IF EXISTS schedule;

CREATE TABLE schedule (scheduleid int(11) NOT NULL AUTO_INCREMENT, start_time time NULL DEFAULT NULL, end_time time NULL DEFAULT NULL, brightness int(3) DEFAULT NULL, start_date date DEFAULT NULL, end_date date DEFAULT NULL, PRIMARY KEY (scheduleid));

INSERT INTO schedule VALUES (1, '18:00:00', '06:00:00', 20, '2014-04-10', '2014-04-20'), (2, '17:00:00', '05:30:00', 80, '2014-04-01', '2014-04-02'), (3, '17:30:00', '06:00:00', 80, '2014-03-27', '2014-03-28');

DROP TABLE IF EXISTS sched_cluster;

CREATE TABLE sched_cluster (scheduleid int(11) NOT NULL, clusterid int(11) DEFAULT NULL, userid int(11) DEFAULT NULL);

INSERT INTO sched_cluster VALUES (1, 1, 1), (1, 2, 1);

DROP TABLE IF EXISTS user_;

CREATE TABLE user_ (username varchar(40) NOT NULL, password varchar(45) NOT NULL, level int(2) NOT NULL DEFAULT '0', emailadd varchar(45) NOT NULL, state varchar(45) NOT NULL, userid int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (userid));

INSERT INTO user_ VALUES ('admin','21232f297a57a5a743894a0e4a801fc3',1,'admin@email.com','',1),('user','ee11cbb19052e40b07aac0ca060c23ee',0,'user@email.com','',2),('gabo','4a34588683fbb3c0cc3e53049048da05',1,'gfvillorente@ittc.up.edu.ph','',3);
