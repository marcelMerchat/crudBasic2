CRUD Website Demonstration

The Create, Read, Update, and Delete (CRUD) website provides forms to enter
data for resumes of job candidates. Managers enter resume data for as
many job candidates as desired in the website database. The resumes can be
seen on the View page. The resumes are reports generated from the database
by means of SQL queries and a PHP server program at the website that creates
the appropriate HTML code to display it.

The website is based on the Capstone project for the University of Michigan
web-development course on Coursera. To avoid publishing a copy
of the Capstone assignment as well as improve it, this version of
a CRUD website was developed. The website does not work without the
database. The SQL commands to setup the database are provided at the end.

###########################################################################

Here is a summary of changes compared to the Capstone project:

There is a guest login where visitors can enter and edit
resumes as well as view resumes made by guests using the login name
'guest@mycompany.com' with password 'login123'. Visitors can obtain their own
password and receive a confirming e-mail automatically to the email they submit
for their account; they must change their password within thirty minutes.
If visitors forget their password they can recover with the email address.

At the welcome page, the user sees a summary list of job candidates entered by
users or managers. Resumes can be viewed without logging-in by clicking a link
on the welcome page, but after logging-in, users only see their own resumes.
Links are provided for adding new resumes, editing existing ones,
or deleting resumes. Filters protect the browser from cross-site
injection (XXS) whenever user data is presented.

Profile:

Compared to the original project where the View page was a report on a
person's profile, the View page has been changed to look more like a resume.
Saving a profile on the "Add" page requires at a minimum, an acceptable
first and last name as well as an email. The other possible entries are options
and can always be updated on the "Edit" page.

There is a new entry box for the person's occupation. The original headline
text-area box is now called 'Goals.'

Job Skills:

There is a new list of job skills that follows the original institution names.
The Skill-Set Table references the skill ID as a foreign key for the Skill
Table just as the Education Table references the name of the school as a
foreign key in the Institution Table. Users are provided with a list of
existing skills to pick from by means of JSON enquiries to the website
just as the names of schools was provided in the original project.

Education:

A degree and major subject Award box was added to the Education Table.
The education table now has a triple many-to-one relationship based on
three foreign keys that including a new degree award column.
This allows more than one educational entry for the same school as long as the
degree or certificate provides a unique triplet of foreign key references.
The original Education Table was a many-to-many table that provided a unique
combination of profile ID and an institution ID.

Work experience:

The work experience has been expanded into separate company name and
job-summary blocks. A future improvement would be to handle company names
in the same manner as education institutions.

###########################################################################

Offensive Language Blocking:

This demo website permits the public to enter information that is publicly
visible. To help protect children and adults from unnecessary harassing language
and provide pages suitable for a general audience, entered data is screened for
offensive language, increasing the data verification performed with JavaScript
for the browser as well as final checking at the website using PHP.
This is accomplished in the browser by borrowing the JSON request method
from the Web-Development course where the 'school.php'
file at the website searched the database for existing schools.
Using a similar JSON technique, text entered by the user is received in the file
'jsonLanguage.php' at the website and the Dictionary and Offensive word
database tables are queried. The file 'jsonLanguage.php' is nearly identical
to 'school.php' file for the capstone project.

The language filter algorithm starts by removing punctuation
and exploding the text phrase into an array of words which are automatically
accepted if they are in the dictionary table. If the words are not in the
dictionary, the data is still entered into the database after further filtering
for offensive words using the Offensive table. This method helps eliminate
unnecessary rejections of legitimate words. The offensive word list only exists
in unreadable coded form in the database in the Cloud website.

The dictionary was constructed for another Coursera Capstone Project based on
Twitter chat data for the Data Science Certificate by Johns Hopkins University.
A SQL loop was used to load the dictionary of approximately 11,000 English
words into the Dictionary Table. For the Offensive Table, the Github
offensive word list of about 100 words that was used for the John Hopkins
Capstone project for text prediction was loaded into the Offensive Table.
I compiled for the English Dictionary for the Coursera text prediction project
using the raw Twitter data by filtering the uncommon words.

###########################################################################

Login Password Assignment by Email:

The email confirmations after obtaining new passwords are handled by means of
the 'PHPMailer' package installed using the Compose Package. These are
available without cost. The code also works in the test environment on
localhost.

###########################################################################

JavaScript Empowers Users:

(a) Creating Data Entry Elements

A further enhancement was the use of JavaScript to change the layout for mobile
devices by making DOM elements directly, which is an alternate way of page
modification in addition to the hot element insertion blocks and the jQuery
append methods.

(b) Adapting the Presentation Layout for Small Devices Like Phones

JavaScript is used to place the start and final year entry boxes on the same
line for wide screens. Whether this helps very much in this particular case
compared to other methods is open to question
but at least an additional method is included; the code for this
is method is rather detailed and long. An if statement could also have been
used with the JQuery append method from the course instead. This is mainly
kept as a template of reusable code for a future website.

(c) For final data validation before the browser submits data to the website,
a variable JavaScript associated array is used to keep track of the character
counts of the data form boxes added by the user. A different basic
array or simple vector list holds the entry box ID numbers for boxes
which have been clicked but not tested yet.

(d) Whenever a data-entry box is clicked:
- A new element is added to the simple vector list or stack using the array
  push method that places the new element on top of the stack. A new key-pair
  is added to the associated array with an initial value of -1.
- An offensive language test by means of a JSON request is started if its
  character count is different than the stored value in the assocaiated array.
  Since the box that was clicked is often empty, the test is carried out for
  the element on the bottom of the stack array after which it is removed
  from the array. The new character count is stored in the associated array.
- If there is a problem with the language test, a message pops-up alerting
  the user and the box appearance changes using the JQuery Library.
  If the test result is positive, the box appearance is reset.

  The click initiated process eliminates delays if the offensive language
  JSON requests were performed when the form was submitted to the website.

###########################################################################

Final Data Validation:

When the data is submitted to the website, all data boxes in the
associated array list as well as lists for the numeric boxes are checked
for completeness. In addition, the last data box that was clicked is checked
for offensive language using an asynchronous AJAX request that prevents
the form being submitted until an all-clear JSON response is received from
the website. Finally, all of the data validation is repeated at the
website before anything is added to database. Final validation at the
website was tested with the JavaScript protection defeated.

###########################################################################

Cloud Website at Digital Ocean:

The site was developed using the Apache2 server on localhost in the
Microsoft Ubuntu extension for Windows 10. The online site runs on nginx
server in an Ubuntu cloud droplet at Digital. I had read about how Dean Attali
had setup his own website for RStudio Data Science reports and was attempting
to duplicate his directions.

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

I am still interested in hosting the RStudio reports; however, I had just
completed the University of Michigan course and decided to install the
CRUD project instead. Since I have hundreds of hours invested in setting up
the CRUD project and getting all of the above
to work, it's hard to justify an experiment that involves installing a second
RStudio server program on the same droplet as the CRUD project. Since I can have
50 data science reports at RStudio for 100 dollars a year.

###########################################################################

Setup Database:

To get started run the following SQL commands:

CREATE DATABASE team; (Human Resources)

// GRANT ALL PRIVILEGES ON team.* TO 'username'@'localhost' IDENTIFIED BY 'password';
// GRANT ALL PRIVILEGES ON team.* TO 'umsi'@'localhost' IDENTIFIED BY 'php123';

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
   password_time TIMESTAMP,
   initial_time TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
   timeout BOOLEAN DEFAULT 1,
   block BOOLEAN DEFAULT 0,
   INDEX(email)
) ENGINE=InnoDB CHARSET=utf8;

INSERT INTO users (name,email,password, random)
    VALUES ('Elvis','epresley@musicland.edu','eaa52980acd0bcfd0937ee5110c74817','XyZzy12*_');
// password is 'rock123'

// ALTER TABLE `users` DROP `hint`;

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

   CONSTRAINT profile_ibfk_1
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

CREATE TABLE Award (
  award_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  UNIQUE(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO Award (name)
           VALUES ('Bachelor of Science in Music') ;

######################################################################

Many-to-Many table: (Revised from Capstone Project)

CREATE TABLE Education (
  profile_id INTEGER,
  institution_id INTEGER,
  award_id INTEGER,
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

  CONSTRAINT education_ibfk_3
  FOREIGN KEY (award_id)
  REFERENCES Award (award_id)
  ON DELETE CASCADE ON UPDATE CASCADE,

  PRIMARY KEY(profile_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#############################################################

Updating the original table:

UPDATE `Education` SET `award_id` = '1'
WHERE `Education`.`profile_id` = 277 AND
 `Education`.`institution_id` = 21 AND
  `Education`.`award_id` = 0;

  UPDATE `Education` SET `award_id` = '1'
  WHERE `Education`.`profile_id` = 5 AND
     `Education`.`institution_id` = 2 AND
     `Education`.`award_id` = 0;

UPDATE `Education` SET `award_id` = '1'
WHERE `Education`.`profile_id` = 5 AND `Education`.`institution_id` = 8 AND
      `Education`.`award_id` = 0

##############################################################
 Change major column to award_id

ALTER TABLE `Education` ADD `award_id` INT NOT NULL AFTER `institution_id`;

###############################################################

Change two-to-one table to three-to-one table

PHPMYADMIN apparently requires dropping the existing foreign keys when making
changes, then re-add them if needed.

ALTER TABLE Education DROP FOREIGN KEY education_ibfk_1;
ALTER TABLE Education DROP FOREIGN KEY education_ibfk_2;
ALTER TABLE Education DROP PRIMARY KEY;

Drop the index for institution_id. Adding the primary key automatically creates
these required indexes:
`ALTER TABLE Education DROP INDEX education_ibfk_2;`

Before adding foreign keys a three-way primary key for a many-to-many table
was first added:

ALTER TABLE `Education` ADD PRIMARY KEY
( `profile_id`, `institution_id`, `award_id`);

(not needed)
ALTER TABLE `Education` ADD INDEX `award_id` (`award_id`) USING BTREE;
ALTER TABLE `Education` ADD INDEX 'profile_id' (`profile_id`) USING BTREE;

Now add the foreign keys:

To avoid an error, first insert a preliminary entry in the Award Table.
The same is required for the Profile and Institution Tables. Then assign
the new foreign key value for the Award Table to all of the records in
the Institution Table. Existing foreign key values are required for the
Profile and Institution Tables also.

ALTER TABLE `Education` ADD CONSTRAINT `education_ibfk_1`
FOREIGN KEY (`profile_id`)
REFERENCES `Profile`(`profile_id`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Education` ADD CONSTRAINT `education_ibfk_2`
FOREIGN KEY (`institution_id`)
REFERENCES `Institution`(`institution_id`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Education` ADD CONSTRAINT `education_ibfk_3`
FOREIGN KEY (`award_id`)
REFERENCES `Award`(`award_id`)
ON DELETE CASCADE ON UPDATE CASCADE;
​
#####################################################################

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

#########################################################################

REFERENCE

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

#########################################################################

THE END
