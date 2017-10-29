-- drop table statements
/*DROP TABLE Afstudeerder CASCADE constraints;
DROP TABLE Begeleider CASCADE constraints;
DROP TABLE Project CASCADE constraints;
DROP TABLE Stagebegeleider CASCADE constraints;
DROP TABLE Stageplek_van CASCADE constraints;

-- tables

CREATE TABLE Begeleider(
	DocentID	NUMBER(7,0),
	BegeleiderNaam	VARCHAR2(30),
	BegEMAIL	VARCHAR2(30),
	BegeleiderTel	NUMBER(10,0),
	
	PRIMARY KEY(DocentID)
);

CREATE TABLE Afstudeerder(
	StudentID	NUMBER(7,0),
	StudentNaam	VARCHAR2(30),
	StuEMAIL	VARCHAR2(30),
	StudentTel	NUMBER(10,0),
	
	PRIMARY KEY(StudentID)
);

CREATE TABLE Project(
	ProjectNaam	VARCHAR2(30),
	Beschrijving	TEXT,
	Voortgang	TEXT,
	
	PRIMARY KEY(ProjectNaam)
);

CREATE TABLE Stagebegeleider(
	BedrijfNaam	VARCHAR2(30),
	SBegeleiderNaam	VARCHAR2(30),
	StaEMAIL	VARCHAR2(30),
	StageTel	NUMBer(10,0),
	
	PRIMARY KEY(BedrijfNaam, SBegeleiderNaam)
);

CREATE TABLE Stageplek_van(
	PlekNaam	VARCHAR2(30),
	Locatie		VARCHAR2(30),
	Tijden		VARCHAR2(30),
	
	PRIMARY KEY(PlekNaam, BedrijfNaam),
	FOREIGN KEY(BedrijfNaam) REFERENCES StageBegeleider,
	ON DELETE CASCADE
);

-- relationships

CREATE TABLE Begeleid(
	DocentID	NUMBER(7,0),
	StudentID	NUMBER(7,0),
	
	PRIMARY KEY (DocentID, StudentID),
	FOREIGN KEY (DocentID) REFERENCES Begeleider,
	FOREIGN KEY (StudentID) REFERENCES Afstudeerder
);

CREATE TABLE Volbrengt(
	StudentID	NUMBER(7,0),
	ProjectNaam	VARCHAR2(30),
	
	PRIMARY KEY(StudentID, ProjectNaam),
	FOREIGN KEY(StudentID) REFERENCES Afstudeerder,
	FOREIGN KEY(ProjectNaam) REFERENCES Project
);

CREATE TABLE Beslaat(
	ProjectNaam	VARCHAR2(30),
	PlekNaam VARCHAR2(30),
	
	PRIMARY KEY(ProjectNaam, PlekNaam, BedrijfNaam),
	FOREIGN KEY(ProjectNaam) REFERENCES Project,
	FOREIGN KEY(PlekNaam) REFERENCES Stageplek_van,
	FOREIGN KEY(BedrijfNaam) REFERENCES Stagebegeleider
);



*/

--Deze tabellen kan je nu aanmaken
-- drop table statements
DROP TABLE Afstudeerder CASCADE constraints;
DROP TABLE Begeleider CASCADE constraints;
DROP TABLE Project CASCADE constraints;
DROP TABLE Stagebegeleider CASCADE constraints;
DROP TABLE Stageplek_van CASCADE constraints;

-- tables

CREATE TABLE Begeleider(
	DocentID	INT(7),
	BegeleiderNaam	VARCHAR(30),
	BegEMAIL	VARCHAR(30),
	BegeleiderTel	INT(10),
	
	PRIMARY KEY(DocentID)
);

CREATE TABLE Afstudeerder(
	StudentID	INT(7),
	StudentNaam	VARCHAR(30),
	StuEMAIL	VARCHAR(30),
	StudentTel	INT(10),
	
	PRIMARY KEY(StudentID)
);

CREATE TABLE Project(
	ProjectNaam	VARCHAR(30),
	Beschrijving	TEXT,
	Voortgang	TEXT,
	Tijd		TEXT, --tijd restricties op een project, bijv voor of najaar
	Studentqualities TEXT, --wat een stage begeleider van een student wilt
	Topic		VARCHAR(127),
	Internship	INT(1), --of het een stage of project van de uni is
	DocentID	INT(7),
	
	PRIMARY KEY(ProjectNaam),
	FOREIGN KEY(DocentId) REFERENCES Begeleider(DocentID)
);

CREATE TABLE Stagebegeleider(
	BedrijfNaam	VARCHAR(30),
	SBegeleiderNaam	VARCHAR(30),
	StaEMAIL	VARCHAR(30),
	StageTel	INT(10),
	
	PRIMARY KEY(BedrijfNaam, SBegeleiderNaam)
);

CREATE TABLE Stageplek_van(
	ProjectNaam	VARCHAR(30),
	PlekNaam	VARCHAR(30), --stad
	Locatie		VARCHAR(30), --straat
	StraatNr	VARCHAR(30), --straatnr
	--naar project toe gegaan Tijden		VARCHAR(30),
	Travel		INT(1), --reiskosten vergoed of niet, ja/nee
	Tnotes		VARCHAR(30), --extra opmerkingen over reiskosten
	Pay		VARCHAR(30), --hoeveel je betaald krijgt op de stage
	BedrijfNaam	VARCHAR(30), 
	
	PRIMARY KEY(ProjectNaam),--, PlekNaam, BedrijfNaam),
	FOREIGN KEY(ProjectNaam) REFERENCES Project(ProjectNaam),
	FOREIGN KEY(BedrijfNaam) REFERENCES Stagebegeleider(BedrijfNaam) ON DELETE CASCADE
);

-- relationships

CREATE TABLE Begeleid(
	DocentID	INT(7),
	StudentID	INT(7),
	
	PRIMARY KEY (DocentID, StudentID),
	FOREIGN KEY (DocentID) REFERENCES Begeleider(DocentID),
	FOREIGN KEY (StudentID) REFERENCES Afstudeerder(StudentID)
);

CREATE TABLE Volbrengt(
	StudentID	INT(7),
	ProjectNaam	VARCHAR(30),
	
	PRIMARY KEY(StudentID, ProjectNaam),
	FOREIGN KEY(StudentID) REFERENCES Afstudeerder(StudentID),
	FOREIGN KEY(ProjectNaam) REFERENCES Project(ProjectNaam)
);

CREATE TABLE Beslaat(
	ProjectNaam	VARCHAR(30),
	--PlekNaam VARCHAR(30),
	BedrijfNaam	VARCHAR(30),
	
	--PRIMARY KEY(ProjectNaam, PlekNaam, BedrijfNaam),
	PRIMARY KEY(ProjectNaam, BedrijfNaam),
	FOREIGN KEY(ProjectNaam) REFERENCES Project(ProjectNaam),
	--FOREIGN KEY(PlekNaam) REFERENCES Stageplek_van(PlekNaam),
	FOREIGN KEY(BedrijfNaam) REFERENCES Stagebegeleider(BedrijfNaam)
);



