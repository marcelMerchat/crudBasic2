# crudBasic

The Create, Read, Update, and Delete (CRUD) website and associated database was developed starting with from the Capstone project for the University of Michigan web development course on Coursera. However, the site is modified for my cloud website and to make it clearly different from the Capstone. Here is a summary of the differences.

Online visitors can register on-line and obtain their own password and receive a confirming e-mail that is automatically sent from the site. The code also works in the test environment on localhost. If visitors forget their password they can recover their account if they remember the hint. The email is handled by means of the PHPMailer freeware package installed using the compose package. The site was developed using the Apache2 server on localhost in the Microsoft Ubuntu extension for Windows 10 and the online site  runs on nginx server in an Ubuntu cloud droplet.

At the welcome page, the user can see a summary list of the profiles except for profiles made with the guest login. The Resume View webpage can be viewed using a link without logging in. Upon logging-in, the user can only see their own profiles.
The profile information is enhanced to be more like an person's resume. There is new entry for the person's occupation. The current headline textbox is called Goals.
The profile is followed by a list of the person’s skills. Skills are  Skill-Set table with multiple entries for each profile is very similar to the Education table. The Skill-Set table is used in conjunction with a Skill table just as the Education table is used with an Institution table.
Similarly, a major subject box was added to education. The work experience has similarly been expanded into separate company and summary blocks.

There are two new database tables named Dictionary and Offensive.  Extending my SQL course work, a programmed loop loaded the 11,000 word dictionary into the database from a delimited text file. The data is screened for offensive language to help make the main page suitable for a general audience, increasing the data verification performed with JavaScript for the browser and further checking at the website. After removing punctuation and exploding text phrases into an array, the information is automatically entered into the database if the words exist in the dictionary of 11,000 English words. If the words are not in the dictionary, the data is still entered into the database after further filtering for offensive words using the Offensive table. This method helps eliminate unnecessary rejections of legitimate words by reducing the need for offensive word checking. The offensive word list only exists in unreadable coded form in the database. The dictionary was constructed for my text-prediction Capstone Project using Twitter chat data for the Data Science Certificate by Johns Hopkins University on Coursera. The GitHub offensive word list of about 100 words was taken from the same project.

A further enhancement was the use of JavaScript to directly construct new DOM elements based on a logical if condition for mobile device detection adding to the course project techniques of hot element insertion block as well as the jQuery append method. The inline insertion block method permits modify certain words such as edu1, edu2, ect; but the other method can change anything. An if statement could also be used with the append method so it's not clear how much direct construction of DOM elements helped and it used a number of lines of code that require a certain level of understanding.

###########################################################################

Short Version

The Create, Read, Update, and Delete (CRUD) website and associated database was developed starting with from the Capstone project for the University of Michigan web development course on Coursera. However, the site is modified for my cloud website and to make it clearly different from the Capstone. Online visitors can register on-line and obtain their own password and receive a confirming e-mail that is automatically sent from the site. If visitors forget their password they can recover their account if they remember the hint. The email is handled by means of the PHPMailer package installed using the compose package. The site was developed using the Apache2 server on localhost in the Microsoft Ubuntu extension for Windows 10 and the online site runs on nginx server in an Ubuntu cloud droplet. At the welcome page, the user can see a summary list of the profiles except for profiles made with the guest login. The Resume View webpage can be viewed using a link without logging in. Upon logging-in, the user can only see their own profiles. The profile information is enhanced to be more like a person's resume. There is a new entry for the person's occupation. The HTML 'textarea' element called 'headline' is now called 'goals.' The profile is followed by a new list of job skills. Similar to the existing education and institution tables, there are Skill-Set and Skill tables where job skills are provided to the user from the existing skills via a JSON request. The work experience has been expanded into separate company and summary blocks. Similarly, a major subject was added to education. There are two new database tables named Dictionary and Offensive. Information is automatically entered into the database if the words exist in the dictionary of 11,000 English words. If the words are not in the dictionary, the data is still entered into the database after further filtering for offensive words using the Offensive table. A further enhancement was the use of JavaScript to directly construct new DOM elements based on a logical if condition for mobile device detection adding to the course project techniques of hot element insertion block as well as the jQuery append method.

###########################################################################

Linux Commands:

For Windows 10 Ubuntu, copy text-editing folder in Windows environnment to Ubuntu Apache2 server folder
sudo cp /mnt/c/Users/merch/Documents/edu/web_developer/mmdotcom/crudbasic/* /var/www/html/crudBasic

Copy droplet repo to crudBasic folder
sudo cp /home/gramps/repos/crudBasic/* /var/www/marcel-merchat.com/html/crudBasic

###########################################################################

Database:

To get started run the following SQL commands:

CREATE DATABASE team; (Human Resources)
//GRANT ALL ON team.* TO 'fred'@'localhost' IDENTIFIED BY 'zap';
//GRANT ALL ON team.* TO 'fred'@'127.0.0.1' IDENTIFIED BY 'zap';
// 'zap' is just a password-related string
// Create the username for the database db_name

//GRANT ALL PRIVILEGES ON team.* TO 'username'@'localhost' IDENTIFIED BY 'password';
//GRANT ALL PRIVILEGES ON team.* TO 'umsi'@'localhost' IDENTIFIED BY 'php123';
  GRANT ALL PRIVILEGES ON team.* TO 'gramps^77'@'localhost' IDENTIFIED BY 'mcp2tWc'; (email: merchatDataTools@gmail.com)

############################################################################


// Use the database
USE team; // (Or select team, misc and so on in phpMyAdmin)

CREATE TABLE users (
   user_id INTEGER NOT NULL
     AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128),
   hint VARCHAR(128),
   INDEX(email)
) ENGINE=InnoDB CHARSET=utf8;

INSERT INTO users (name,email,password, hint)
    VALUES ('gramps^77','merchatDataTools@gmail.com','e76500c3d37247cb0564d800821a1311','frank');
// password is 'mcp2tWc'; gramps77 is also the designed owner for pdo objects in php.   

INSERT INTO users (name,email,password, hint) VALUES ('guest','guest@mycompany.com','bd244d460a85ee6e0883dbf56bcd30b6','myhint');
                    'login123' corresponds to hashcode 'bd244d460a85ee6e0883dbf56bcd30b6'
// password is 'login123'

INSERT INTO users (name,email,password, hint)
    VALUES ('Elvis','epresley@musicland.edu','eaa52980acd0bcfd0937ee5110c74817','myhint');
// password is 'rock123'

ALTER TABLE users ADD INDEX(password);


#######################################################################

CREATE TABLE Profile (
       profile_id INTEGER NOT NULL AUTO_INCREMENT,
       user_id INTEGER NOT NULL,
       first_name VARCHAR(128),
       last_name VARCHAR(128),
       email VARCHAR(128),
       profession VARCHAR(128),
       goal Text,

       PRIMARY KEY(profile_id),

       CONSTRAINT profile_ibfk_2
       FOREIGN KEY (user_id) REFERENCES users(user_id)
       ON DELETE CASCADE ON UPDATE CASCADE
       ) ENGINE=InnoDB CHARSET=utf8;

INSERT INTO Profiles (user_id, first_name, last_name, email, profession, goal)
           VALUES (1, 'Elvis', 'Presley', 'epresley@musicland.com', 'great singer', 'Changed America') ;
INSERT INTO Profile (user_id, first_name, last_name, email, profession, goal)
            VALUES (1, 'Marilyn', 'Monroe', 'mmonroe@hollyland.com', 'great actress', 'America Icon, Changed the world.') ;
INSERT INTO Profiles (user_id, first_name, last_name, email, profession, goal)
                            VALUES (1, 'U', 'MSI', 'umsi@umich.edu', 'great coach', 'Inspiration to students') ;


#######################################################################

CREATE TABLE Skill (
skill_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(128) NOT NULL DEFAULT '',
profile_id INTEGER,
description VARCHAR(255),
INDEX(description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#######################################################################

Many-to-Many table:

CREATE TABLE SkillSet (
profile_id INTEGER,
skill_id INTEGER,
rank INTEGER,

CONSTRAINT skillset_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT skillset_ibfk_2
FOREIGN KEY (skill_id)
REFERENCES Skill (skill_id)
ON DELETE CASCADE ON UPDATE CASCADE,

PRIMARY KEY(profile_id, skill_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;      

#######################################################################

CREATE TABLE Position (
position_id INTEGER NOT NULL AUTO_INCREMENT,
profile_id INTEGER,
rank INTEGER,
yearStart INTEGER,
yearLast INTEGER,
organization VARCHAR(128),
description TEXT,
PRIMARY KEY(position_id),

CONSTRAINT position_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

######################################################################

CREATE TABLE Institution (
institution_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(255),
UNIQUE(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

######################################################################

Many-to-Many table:

CREATE TABLE Education (
profile_id INTEGER,
institution_id INTEGER,
rank INTEGER,
year INTEGER,

CONSTRAINT education_ibfk_1
FOREIGN KEY (profile_id)
REFERENCES Profile (profile_id)
ON DELETE CASCADE ON UPDATE CASCADE,

CONSTRAINT education_ibfk_2
FOREIGN KEY (institution_id)
REFERENCES Institution (institution_id)
ON DELETE CASCADE ON UPDATE CASCADE,

PRIMARY KEY(profile_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#########################################################################

drop table if exists Dictionary;
CREATE TABLE Dictionary
(
Word VARCHAR(128) NOT NULL PRIMARY KEY,
Freq INTEGER
) ENGINE=InnoDB CHARSET=utf8;

#########################################################################

LOAD DICTIONARY:

The text file containing the words to be loaded must be put in this special
folder in order to be accessed by MySQL.

Ubuntu for Windows-10: /var/lib/mysql-files

Copy it to the folder with a command like this:
sudo cp /mnt/c/ProgramData/MySQL/"MySQL Server 5.7"/Uploads/dictionary.csv /var/lib/mysql-files

----------

CREATE TABLE Offensive (
   user_id INTEGER NOT NULL
     AUTO_INCREMENT PRIMARY KEY,
   word VARCHAR(128)

) ENGINE=InnoDB CHARSET=utf8;

drop table if exists Dictionary;
CREATE TABLE Dictionary
(
Word VARCHAR(128) NOT NULL PRIMARY KEY,
Freq INTEGER
) ENGINE=InnoDB CHARSET=utf8;

LOAD DATA INFILE '/var/lib/mysql-files/dictionary.csv'
INTO TABLE Dictionary
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS

LOAD DATA INFILE '/var/lib/mysql-files/dictionary.csv'
INTO TABLE junk
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
