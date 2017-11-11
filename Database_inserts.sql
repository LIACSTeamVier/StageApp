INSERT INTO Supervisor VALUES ('1553968', 'relationshiptester', 'martenklaver@hotmail.com', 12345, 'yes', 'yes', 'BOTH');
INSERT INTO Supervisor VALUES ('sup.sam', 'Sam Super', 'benstef2015@gmail.com', 321, 'yes', 'no', 'BUS');

INSERT INTO Student VALUES ('321', 'steve', 'test@test.com', '112');
INSERT INTO Student VALUES ('s1234567', 'Stan Student', 'stanatemaildotcom', '911');
INSERT INTO Student VALUES ('s1551396', 'Benjamin Steffens', 'benstef2015@gmail.com', '123456789');

INSERT INTO Internship_Contact VALUES ('tom.testcorp', 'TestCorp', 'tom', 'test@test.test', '12345');

INSERT INTO Project VALUES ('Fun Project.', 'This is a fun project.', 'No progress yet.', 'five hours.', 'Need to have completed the tutorial', 'Fun', 0, 'sup.sam', NULL, NULL, NULL);
INSERT INTO Project VALUES ('test', 'php test uni project', NULL, '', '', 'php', 0, '1553968', NULL, NULL, NULL);
INSERT INTO Project VALUES ('teststagesdfdsf', 'datamining', NULL, 'all night long', 'slim', 'testing', 1, NULL, NULL, 'TestCorp', 'tom');

INSERT INTO Internship_of VALUES ('teststagesdfdsf', 'leiden', '1', '6', NULL, 'Geen trein', '100€ per hour', 'TestCorp');

INSERT INTO RelationOptions VALUES ('First Supervisor');
INSERT INTO RelationOptions VALUES ('Second Supervisor');

INSERT INTO Supervises VALUES ('First Supervisor', '1553968', '321', 1, '1XMqF2R3AdUIk0CRgimPFJQYsT2ZwWjw');
INSERT INTO Supervises VALUES ('Second Supervisor', '1553968', '321', 1, 'wWllu26vdHTkMiDf8j0TFJs5BMLynpno');

INSERT INTO Part_of VALUES ('teststagesdfdsf', 'TestCorp');
