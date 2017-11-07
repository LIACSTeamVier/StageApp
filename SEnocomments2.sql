CREATE TABLE StageApp_Gebruikers(
	Identifier	VARCHAR(30),
	Klasse	VARCHAR(30),
	Naam	VARCHAR(30),
	Password	VARCHAR(30),
	
	PRIMARY KEY(Identifier)
);

CREATE TABLE Begeleider(
	DocentID	VARCHAR(30),
	BegeleiderNaam	VARCHAR(30),
	BegEMAIL	VARCHAR(30),
	BegeleiderTel	INT(10),
	RoleFirst       VARCHAR(3),
	RoleSecond      VARCHAR(3),
	Background      VARCHAR(4),
	
	PRIMARY KEY(DocentID),
	FOREIGN KEY(DocentID) REFERENCES StageApp_Gebruikers(Identifier)
);

CREATE TABLE Afstudeerder(
	StudentID	VARCHAR(30),
	StudentNaam	VARCHAR(30),
	StuEMAIL	VARCHAR(30),
	StudentTel	INT(10),
	
	PRIMARY KEY(StudentID),
	FOREIGN KEY(StudentID) REFERENCES StageApp_Gebruikers(Identifier)
);

CREATE TABLE Stagebegeleider(
    SBegeleiderID VARCHAR(30) UNIQUE,
	BedrijfNaam	VARCHAR(30),
	SBegeleiderNaam	VARCHAR(30),
	StaEMAIL	VARCHAR(30),
	StageTel	INT(10),
	
	PRIMARY KEY(BedrijfNaam, SBegeleiderNaam),
	FOREIGN KEY(SBegeleiderID) REFERENCES StageApp_Gebruikers(Identifier)
);

CREATE TABLE Project(
	ProjectNaam	VARCHAR(30),
	Beschrijving	TEXT,
	Voortgang	TEXT,
	Tijd		TEXT,
	Studentqualities TEXT,
	Topic		VARCHAR(127),
	Internship	INT(1),
	DocentID	VARCHAR(30),
	SBegeleiderID VARCHAR(30),
	BedrijfNaam	VARCHAR(30),
	SBegeleiderNaam VARCHAR(30),
	
	PRIMARY KEY(ProjectNaam),
	FOREIGN KEY(DocentID) REFERENCES Begeleider(DocentID),
	FOREIGN KEY(SBegeleiderID) REFERENCES Stagebegeleider(SBegeleiderID),
	FOREIGN KEY(BedrijfNaam, SBegeleiderNaam) REFERENCES Stagebegeleider(BedrijfNaam, SBegeleiderNaam)
);

CREATE TABLE Stageplek_van(
	ProjectNaam	VARCHAR(30),
	PlekNaam	VARCHAR(30),
	Locatie		VARCHAR(30),
	StraatNr	VARCHAR(30),
	Travel		INT(1),
	Tnotes		VARCHAR(30),
	Pay		VARCHAR(30),
	BedrijfNaam	VARCHAR(30), 
	
	PRIMARY KEY(ProjectNaam),
	FOREIGN KEY(ProjectNaam) REFERENCES Project(ProjectNaam),
	FOREIGN KEY(BedrijfNaam) REFERENCES Stagebegeleider(BedrijfNaam) ON DELETE CASCADE
);

CREATE TABLE RelationOptions(
    type VARCHAR(20),
    
    PRIMARY KEY (type)
);

CREATE TABLE Begeleid(
    type        VARCHAR(20),	
    DocentID	VARCHAR(30),
    StudentID	VARCHAR(30),
    Accepted	INT(1),
    ActivationCode	VARCHAR(32) UNIQUE,
	
    PRIMARY KEY (type, StudentID),
    FOREIGN KEY(type) REFERENCES RelationOptions(type),
    FOREIGN KEY(DocentID) REFERENCES Begeleider(DocentID),
    FOREIGN KEY(StudentID) REFERENCES Afstudeerder(StudentID)
);

CREATE TABLE Volbrengt(
	StudentID	VARCHAR(30),
	ProjectNaam	VARCHAR(30),
	
	PRIMARY KEY(StudentID, ProjectNaam),
	FOREIGN KEY(StudentID) REFERENCES Afstudeerder(StudentID),
	FOREIGN KEY(ProjectNaam) REFERENCES Project(ProjectNaam)
);

CREATE TABLE Beslaat(
	ProjectNaam	VARCHAR(30),
	BedrijfNaam	VARCHAR(30),
	
	PRIMARY KEY(ProjectNaam, BedrijfNaam),
	FOREIGN KEY(ProjectNaam) REFERENCES Project(ProjectNaam),
	FOREIGN KEY(BedrijfNaam) REFERENCES Stagebegeleider(BedrijfNaam)
);

