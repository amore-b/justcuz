drop table users cascade constraints;
drop table location cascade constraints;
drop table customer cascade constraints;
drop table employee cascade constraints;
drop table member;
drop table manager cascade constraints;
drop table supplier_adds cascade constraints;
drop table merchandise_supplies cascade constraints;
drop table inventory_tracks;
drop table order_delivers_buys;
drop table stored_at;

create table users
	(email varchar(32) not null primary key,
	u_type int,
	check (0 < u_type and u_type < 3));

create table location
	(location_num int not null primary key,
	address varchar(100) null);

grant select on location to public;

create table customer
	(cid int not null primary key,
	name varchar(32) null,
	email varchar(32) not null unique,
	address varchar(100) null,
	card_num int null,
	card_type varchar(10) null,
	foreign key (email) references users ON DELETE CASCADE);

grant select on customer to public;

create table employee
	(eid int not null primary key,
	email varchar(32) not null unique,
	password varchar(32) not null,
	name varchar(32) null,
	address varchar(100) null,
	phone_num varchar(32) null,
	hire_date date null,
	foreign key (email) references users ON DELETE CASCADE);

grant select on employee to public;

create table member
	(cid int not null,
	password varchar(32) not null,
	points float null,
	primary key (cid),
	foreign key (cid) references customer ON DELETE CASCADE);

grant select on member to public;

create table manager
	(eid int not null,
	seniority int null,
	primary key (eid),
	foreign key (eid) references employee ON DELETE CASCADE);

grant select on manager to public;

create table supplier_adds
	(company_name varchar(32) not null primary key, 
	address varchar(100) null,
	phone_num varchar(32) null,
	eid int not null,
	foreign key (eid) references manager ON DELETE CASCADE);

grant select on supplier_adds to public;

create table merchandise_supplies
	(item_num int not null primary key,
	price float null, -- >= 0
	type varchar(32) null,
	gender char(1) null,
	color varchar(10) null,
	company_name varchar(32) not null,
	foreign key (company_name) references supplier_adds ON DELETE CASCADE);

grant select on merchandise_supplies to public;

create table inventory_tracks
	(item_num int not null,
	size_label char(2) not null,
	count int null, -- >= 0
	eid int null,
	primary key (item_num, size_label),
	foreign key (item_num) references merchandise_supplies ON DELETE CASCADE,
	foreign key (eid) references manager ON DELETE CASCADE);

grant select on inventory_tracks to public;


create table order_delivers_buys
	(order_num int not null,
	total float null,
	item_num int not null,
	eid int not null,
	cid int not null,
	order_date date null,
	quantity int null, -- > 0
	primary key (order_num),
	foreign key (item_num) references merchandise_supplies ON DELETE CASCADE,
	foreign key (eid) references employee ON DELETE CASCADE,
	foreign key (cid) references customer ON DELETE CASCADE);

grant select on order_delivers_buys to public;

create table stored_at
	(item_num int not null,
	location_num int not null,
	primary key (item_num, location_num),
	foreign key (item_num) references merchandise_supplies ON DELETE CASCADE,
	foreign key (location_num) references location ON DELETE CASCADE);

grant select on stored_at to public;

insert into location
values(1, '2329 West Mall Vancouver, B.C. Canada, V6T 1Z4');

insert into location
values(2, '74 Green St Tunapuna, Trinidad W.I.');

insert into location
values(3, '6762 33 Ave N St. Petersburg, FL 33710');

insert into location
values(4, '1234 2 Ave Vancouver, B.C. Canada V6K 4S3');

insert into location
values(5, 'Ap #867-859 Sit Rd Azusa, New York, 39531');

insert into users
values('johnoliver@hotmail.com', 2);

insert into users
values('davesmith@gmail.com', 2);

insert into users
values('aaron_g@shaw.ca', 2);

insert into users
values('bradpitt@gmail.com', 2);

insert into users
values('kemerson@gmail.com', 2);

insert into users
values('jj123@gmail.com', 2);

insert into users
values('therealdeal@jc.ca', 1);

insert into users
values('bigboss@jc.ca', 1);

insert into users
values('em@jc.ca', 1);

insert into users
values('eduffy@jc.ca', 1);

insert into users
values('wwjcd@jc.ca', 1);

insert into users
values('lylestyle@jc.ca', 1);

insert into users
values('jbiebs@jc.ca', 1);

insert into users
values('temp@jc.ca', 1);

insert into customer
values(1212, 'John Oliver', 'johnoliver@hotmail.com', 
'1300 Blue Rd, Vancouver, B.C. Canada V1C 2C3', 5154345676548790, 'mastercard');

insert into customer
values(1234, 'David Smith', 'davesmith@gmail.com',
'511-5762 At Rd. Chelsea, MI 67708', 5533789087654567, 'mastercard');

insert into customer
values(1111, 'Aaron Green', 'aaron_g@shaw.ca', 
'5587 Nunc. Avenue Erie, Rhode Island 24975', 3434526767889087, 'amex');

insert into customer
values(2222, 'Brad Pitt', 'bradpitt@gmail.com', 
'1234 Hollywood Rd Hollywood, California', 3723456755498145, 'amex');

insert into customer
values(3098, 'Kieran Emerson', 'kemerson@gmail.com',
'414-7533 Non Rd. Miami Beach, North Dakota 58563', 6011654905838854, 'discover');

insert into customer
values(1200, 'Jordon Judge', 'jj123@gmail.com', 
'7652 4 Ave W Vancouver, B.C. Canada V2Y 2V4', 4231987603697654, 'visa');

insert into employee
values(5000, 'therealdeal@jc.ca', 'emc2','Albert Einstein', '367-674 Mi Street, Greensboro, VT 40684',
'168-222-1592', '1998-03-01');

insert into employee
values(5001, 'bigboss@jc.ca', '2coolFOYOU','Robert Downey Jr', '123 Technology Way, Outer Space', '500-500-5000', '2010-10-28');

insert into employee
values(5002, 'em@jc.ca', 'marsbars', 'Elon Musk', '5020 Crater Lane, Mars', '200-250-1111', '2004-01-01');

insert into employee
values(5003, 'eduffy@jc.ca', 'surewhynot', 'Ezra Duffy', '782-7348 Dis Rd. Austin, KY 50710',
'203-982-6130', '2000-12-13');

insert into employee
values(5004, 'wwjcd@jc.ca', 'DUNNO', 'Jasper Carney', '1195 Lobortis Rd. New Orleans, New Hampshire 71983',
'763-409-5446', '2016-08-01');

insert into employee
values(5005, 'lylestyle@jc.ca', 'saf3ty1Zk3y','Lyle Sutton', '250-9843 Elementum St. South Gate, Missouri 68999',
'736-522-8584', '2016-07-18');

insert into employee
values (5010, 'jbiebs@jc.ca', 'whatdoyoumean', 'Justin Bieber', '4560 Hollywood Blvd', '555-555-5555', '2016-11-18');

insert into employee
values (5222, 'temp@jc.ca', 'temporary', 'Temp Manager', '3040 West 2nd Ave', '250-700-7000', '2016-11-21');

insert into member
values(1212, 'password', 0);

insert into member
values(1234, 'vancouver123', 90);

insert into member
values(1111, 'pickles2016', 1500);

insert into member
values(2222, 'justcuz', 3000);

insert into member
values(3098, 'password321', 0);

insert into manager
values(5000, 1);

insert into manager
values(5001, 2);

insert into manager
values(5002, 3);

insert into manager
values(5003, 4);

insert into manager
values(5004, 5);

insert into manager
values(5222, 1);

insert into supplier_adds
values('Jean Warehouse', '6434 W Broadway Vancouver, B.C. V4E 0C0', '604-555-5555', 5000);

insert into supplier_adds
values('Shiny Things Inc', '137-12100 Riverside Way, Richmond, BC V6W 1K5', '778-884-3434', 5002);

insert into supplier_adds
values('Fresh Kicks Supply', 'P.O. Box 721 902 Dolor Rd. Fremont AK 19408',
'187-582-9707', 5004);

insert into supplier_adds
values('All The Leather', '361-7936 Feugiat St. Williston Nevada 58521',
'774-914-2510', 5002);

insert into supplier_adds
values('Wool You Be Mine', '6216 Aenean Avenue Seattle Utah 81202',
'888-106-8550', 5003);

insert into merchandise_supplies
values(1, 40.99, 'jeans', 'U', 'blue', 'Jean Warehouse');

insert into merchandise_supplies
values(2, 20.05, 'shirt', 'W', 'pink', 'Shiny Things Inc');

insert into merchandise_supplies
values(3, 70.00, 'shoes', 'M', 'white', 'Fresh Kicks Supply');

insert into merchandise_supplies
values(4, 200.50, 'purse', 'W', 'black', 'All The Leather');

insert into merchandise_supplies
values(5, 30.79, 'sweater', 'U', 'orange', 'Wool You Be Mine');

insert into inventory_tracks
values(1, 'XS', 10, 5002);

insert into inventory_tracks
values(2, 'L', 72, 5003);

insert into inventory_tracks
values(3, 'M', 1, 5004);

insert into inventory_tracks
values(4, 'S', 432, 5000);

insert into inventory_tracks
values(5, 'L', 90, 5001);

insert into order_delivers_buys
values(120, 81.98, 1, 5005, 1111, '1999-08-10', 2);

insert into order_delivers_buys
values(4903, 20.05, 2, 5005, 2222, '2005-01-30', 1);

insert into order_delivers_buys
values(5394, 70.00, 3, 5005, 3098, '2009-10-03', 1);

insert into order_delivers_buys
values(204986, 60.15, 2, 5001, 1200, '2013-04-15', 3);

insert into order_delivers_buys
values(343456, 30.79, 5, 5005, 1212, '2015-05-16', 1);

insert into stored_at
values(1, 2);

insert into stored_at
values(1, 3);

insert into stored_at
values(2, 1);

insert into stored_at
values(3, 4);

insert into stored_at
values(4, 2);

insert into merchandise_supplies
values(6, 69.99, 'jeans', 'M', 'black', 'Jean Warehouse');

insert into merchandise_supplies
values(7, 420.99, 'jeans', 'F', 'rainbow', 'Jean Warehouse');

insert into merchandise_supplies
values(8, 69420.99, 'jeans', 'M', 'fuschia', 'Jean Warehouse');

insert into merchandise_supplies
values(9, 32.99, 'jeans', 'U', 'chartreuse', 'Jean Warehouse');

insert into merchandise_supplies
values(10, 20.71, 'shirt', 'W', 'pink', 'Shiny Things Inc');

insert into merchandise_supplies
values(11, 1.75, 'shirt', 'M', 'green', 'Shiny Things Inc');

insert into merchandise_supplies
values(12, 32.00, 'shirt', 'W', 'red', 'Shiny Things Inc');

insert into merchandise_supplies
values(13, 57.80, 'shirt', 'U', 'purple', 'Shiny Things Inc');

insert into merchandise_supplies
values(14, 70.00, 'shoes', 'M', 'yellow', 'Fresh Kicks Supply');

insert into merchandise_supplies
values(15, 60.00, 'shoes', 'U', 'purple', 'Fresh Kicks Supply');

insert into merchandise_supplies
values(16, 5.00, 'shoes', 'W', 'black', 'Fresh Kicks Supply');

insert into merchandise_supplies
values(17, 80.00, 'shoes', 'M', 'white', 'Fresh Kicks Supply');

insert into merchandise_supplies
values(18, 420.50, 'purse', 'W', 'black', 'All The Leather');

insert into merchandise_supplies
values(19, 69.50, 'purse', 'M', 'white', 'All The Leather');

insert into merchandise_supplies
values(20, 69420.50, 'purse', 'U', 'yellow', 'All The Leather');

insert into merchandise_supplies
values(21, 42069.50, 'purse', 'W', 'brown', 'All The Leather');

insert into merchandise_supplies
values(22, 30.71, 'sweater', 'U', 'red', 'Wool You Be Mine');

insert into merchandise_supplies
values(23, 69.79, 'sweater', 'M', 'brown', 'Wool You Be Mine');

insert into merchandise_supplies
values(24, 420.79, 'sweater', 'W', 'purple', 'Wool You Be Mine');

insert into merchandise_supplies
values(25, 69420.79, 'sweater', 'W', 'blue', 'Wool You Be Mine');

insert into inventory_tracks
values(6, 'XS', 400, 5002);

insert into inventory_tracks
values(7, 'L', 720, 5003);

insert into inventory_tracks
values(8, 'M', 800, 5004);

insert into inventory_tracks
values(9, 'S', 432, 5000);

create trigger ballinMember
	after insert on order_delivers_buys
	for each row
	when (new.total > 999)
	begin
	update member set points=points+50000 where cid = :new.cid;
	end;
	/
