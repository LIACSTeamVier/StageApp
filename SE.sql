-- drop table statements
DROP TABLE Afstudeerder CASCADE constraints;
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
