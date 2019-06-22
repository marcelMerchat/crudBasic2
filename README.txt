# crudBasic

The Create, Read, Update, and Delete (CRUD) website provides a way to enter
resumes of job candidates into a database. It was developed from the Capstone
project for the University of Michigan web-development course on Coursera.
To avoid publishing a copy of the Capstone assignment as well as improve it,
this version of a CRUD website was developed. Users enter resume data for as
many job candidates as desired in the website database and to subsequently view
the resumes on another screen. Here is a summary of changes compared to the
Capstone project.

At the welcome page, the user sees a summary list of job candidates entered by
users or managers. Resumes can be viewed without logging-in by clicking a link
on the welcome page, but after logging-in, users only see their own resumes
and they are provided links for adding new resumes, editing an existing one,
or deleting one. There is a guest login where visitors can enter and edit
resumes as well as view resumes made by guests using the login name
'guest@mycompany.com' with password 'login123'. Visitors can obtain their own
password and receive a confirming e-mail automatically to the email they submit
for their account. If visitors forget their password they can recover their
account if they remember the hint.

The View page is more like a resume than a profile. In the original project,
the resume was a view of profile information. Here it is expanded to be a
person's resume. There is a new entry for the person's occupation and the
headline box is now called 'Goals.' There is a new list of job skills that is
similar to the education list with an associated database table for skills
that mirrors the table of educational institutions. Users are provided with
existing skills by means of JSON enquiries to the website. A major subject box
was added to education. The work experience has been expanded into separate
company and summary blocks.

Offensive Language Blocking:

This demo website permits the public to enter information that is publicly
visible. To help protect children and adults from unnecessary harassing language
and provide pages suitable for a general audience, entered data is screened for
offensive language, increasing the data verification performed with JavaScript
for the browser as well as final checking at the website using PHP. There are
two new database tables at the website named Dictionary and Offensive. Extending
my SQL course work, a program loaded the 11,000 word dictionary into the
database from a delimited text file of words. This is accomplished by borrowing
the JSON request method from the Web-Development course where the 'school.php'
file at the website searched the database for existing schools with similar
names and a list of schools was provided in a pop-up for the user.

Using a similar technique, text entered by the user is received in the file
'jsonLanguage.php' at the website and the Dictionary and Offensive word database
tables are searched for each word entered by the user. The file
'jsonLanguage.php' is nearly identical to the 'school.php' file for the
capstone project. The language filter algorithm starts by removing punctuation
and exploding the text phrase into an array of words which are automatically
accepted if they are in the dictionary table. If the words are not in the
dictionary, the data is still entered into the database after further filtering
for offensive words using the Offensive table. This method helps eliminate
unnecessary rejections of legitimate words. The offensive word list only exists
in unreadable coded form in the database at the website.

The dictionary was constructed for another Coursera Capstone Project based on
Twitter chat data for the Data Science Certificate by Johns Hopkins University.
The GitHub offensive word list of about 100 words was taken from that project
for text prediction. The Dictionary table for the new website is loaded with
the words in the single word dictionary I made for the text prediction project
using the raw Twitter data.

A further enhancement was the use of JavaScript to change the layout for mobile
devices by making DOM elements directly, which is an alternate way of page
modification in addition to the hot element insertion blocks and the jQuery
append methods. I used it to place the start and final year entry
boxes on the same line for wide screens. Whether this helps very much is open
to question but at least an additional method is included; the code for this
is method is rather detailed and long. An if statement could also have been used
with the JQuery append method instead. This is mainly kept as a template of
reusable code for a future website.

The email confirmations after obtaining new passwords are handled by means of
the 'PHPMailer' package installed using the Compose Package for PHP. These are
available without cost. The code also works in the test environment on
localhost. The site was developed using the Apache2 server on localhost in the
Microsoft Ubuntu extension for Windows 10 and the online site runs on nginx
server in an Ubuntu cloud droplet.

Notes about the Website:

The online site is a functional computer which exists as a block called a
droplet within a larger server or a Cloud of servers. The droplet is an Ubuntu
Linux system. The PHP web files were tested using an Apache2 server on localhost
in the Microsoft Ubuntu extension for Windows 10. The website droplet had been
previously setup with nginx server following a data scientist on another project
in order to host webpages generated using RStudio. But I had just completed the
University of Michigan Web-Development program and felt it was a better idea to
install the CRUD Capstone Project on the droplet. Since my website works with
both servers and installing the server in the Ubuntu linux environment in the
droplet was not trivial, it's best to leave it as a future project to replace
nginx with Apache in the cloud droplet in order to provide a more uniform
testing environment. I took careful notes for the server installations, but
changing the server probably will require some further debugging.

The cost for the server droplet computer of $5 per month is economical, but the
server and database are installed from scratch in the Linux environment compared
to website running in an assigned folder on a shared computer.

###########################################################################

Linux Commands:

The C: drive for windows exists at '/mnt/c' in the linux environment.

Since it easier to work with the text files in the Window enviroment, copy the
folder from the Windows side to the Ubuntu server folder for testing on
localhost:

sudo cp /mnt/c/Users/merch/Documents/edu/web_developer/mmdotcom/crudbasic/* /var/www/html/crudBasic

Copy droplet repo to crudBasic folder
sudo cp /home/gramps/repos/crudBasic/* /var/www/marcel-merchat.com/html/crudBasic

At this time, the Ubuntu linux side of Windows 10, is only a command-line
environment. This is the main reason for working on the Windows side and copying
the folder to the Ubuntu server folder.

###########################################################################

Database:

To get started run the following SQL commands:

CREATE DATABASE team; (Human Resources)

GRANT ALL PRIVILEGES ON team.* TO 'gramps77'@'localhost' IDENTIFIED BY 'mcp2tWc'; (email: merchatDataTools@gmail.com)
//GRANT ALL PRIVILEGES ON team.* TO 'username'@'localhost' IDENTIFIED BY 'password';
//GRANT ALL PRIVILEGES ON team.* TO 'umsi'@'localhost' IDENTIFIED BY 'php123';

############################################################################

// Use the database
USE team; // (Or select team in phpMyAdmin)

CREATE TABLE users (
   user_id INTEGER NOT NULL
     AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128),
   random VARCHAR(128),
   hint VARCHAR(128),
   INDEX(email)
) ENGINE=InnoDB CHARSET=utf8;

INSERT INTO users (name,email,password,random,hint)
    VALUES ('gramps^77','merchatDataTools@gmail.com','e76500c3d37247cb0564d800821a1311','XyZzy12*_','frank');
UPDATE users SET random = 'XyZzy12*_';

//password is 'mcp2tWc'; gramps77 is also the designed owner for pdo objects
//in php.

INSERT INTO users (name,email,password, hint) VALUES ('guest','guest@mycompany.com','bd244d460a85ee6e0883dbf56bcd30b6','myhint');
                    'login123' corresponds to hashcode 'bd244d460a85ee6e0883dbf56bcd30b6'
// password is 'login123'

INSERT INTO users (name,email,password, hint)
    VALUES ('Elvis','epresley@musicland.edu','eaa52980acd0bcfd0937ee5110c74817','myhint');
// password is 'rock123'

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

##########################################################################

MySQL Command to fill Dictionary table.

LOAD DATA INFILE '/var/lib/mysql-files/dictionary.csv'
INTO TABLE Dictionary
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS

#########################################################################

CREATE TABLE Offensive (
   user_id INTEGER NOT NULL
     AUTO_INCREMENT PRIMARY KEY,
   word VARCHAR(128)

) ENGINE=InnoDB CHARSET=utf8;
