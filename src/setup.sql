CREATE TABLE IF NOT EXISTS cl_bookers 
	(booker_id int(11) NOT NULL AUTO_INCREMENT,
	 booker_name varchar(50) NOT NULL,
	 booker_email varchar(100) NOT NULL,
	 booker_wp_user_id int(11),
	 PRIMARY KEY (booker_id),
	 UNIQUE KEY (booker_email),
	 UNIQUE KEY (booker_wp_user_id));

CREATE TABLE IF NOT EXISTS cl_ticket_types 
	(ticket_type_id int(11) NOT NULL AUTO_INCREMENT,
	 ticket_name varchar(50) NOT NULL,
	 ticket_price DECIMAL(10,2) NOT NULL,
	 PRIMARY KEY (ticket_type_id));

CREATE TABLE IF NOT EXISTS cl_ticket_status 
	(status_id int(11) NOT NULL AUTO_INCREMENT,
	 status_name varchar(50) NOT NULL,
	 PRIMARY KEY (status_id));

CREATE TABLE IF NOT EXISTS cl_payment_methods 
	(payment_method_id int(11) NOT NULL AUTO_INCREMENT,
	 method_name varchar(50) NOT NULL,
	 PRIMARY KEY (payment_method_id));

CREATE TABLE IF NOT EXISTS cl_guestlist 
	(ticket_id int(11) NOT NULL AUTO_INCREMENT,
	 guest_name varchar(50) NOT NULL,
	 status_id int(11) NOT NULL, 
	 booker_id int(11) NOT NULL, 
	 ticket_type_id int(11) NOT NULL, 
	 ticket_meta longtext NOT NULL, 
	 PRIMARY KEY (ticket_id),
	 FOREIGN KEY (booker_id) REFERENCES cl_bookers(booker_id),
	 FOREIGN KEY (ticket_type_id) REFERENCES cl_ticket_types(ticket_type_id),
	 FOREIGN KEY (status_id) REFERENCES cl_ticket_status(status_id));

CREATE TABLE IF NOT EXISTS cl_transactions 
	(transaction_id int(11) NOT NULL AUTO_INCREMENT,
	 ticket_id int(11) NOT NULL,
	 payment_method_id int(11) NOT NULL,
	 payment_note text,
	 PRIMARY KEY (transaction_id),
	 FOREIGN KEY (ticket_id) REFERENCES cl_guestlist(ticket_id),
	 FOREIGN KEY (payment_method_id) REFERENCES cl_payment_methods(payment_method_id));
	