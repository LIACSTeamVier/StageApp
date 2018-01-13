CREATE TABLE InternshipApp_Users(
	Identifier	VARCHAR(50),
	Class	VARCHAR(30),
	Name	VARCHAR(30),
	Password	VARCHAR(60),
	
	PRIMARY KEY(Identifier)
);

CREATE TABLE BackgroundOptions(
    Background VARCHAR(20),
    
    PRIMARY KEY (Background)
);

CREATE TABLE Supervisor(
	SupID	VARCHAR(50),
	SupName	VARCHAR(30),
	SupEMAIL	VARCHAR(50),
	SupTel	VARCHAR(10),
	RoleFirst       VARCHAR(3),
	RoleSecond      VARCHAR(3),
	Background      VARCHAR(20),
	Topics		VARCHAR(144),
	
	PRIMARY KEY(SupID),
	FOREIGN KEY(SupID) REFERENCES InternshipApp_Users(Identifier),
        FOREIGN KEY(Background) REFERENCES BackgroundOptions(Background),
);

CREATE TABLE Student(
	StuID	VARCHAR(30),
	StuName	VARCHAR(30),
	StuEMAIL	VARCHAR(50),
	StuTel	VARCHAR(10),
	
	PRIMARY KEY(StuID),
	FOREIGN KEY(StuID) REFERENCES InternshipApp_Users(Identifier)
);

CREATE TABLE Internship_Contact(
    IConID VARCHAR(50) UNIQUE,
	CompanyName	VARCHAR(30),
	IConName	VARCHAR(30),
	IConEMAIL	VARCHAR(50),
	IConTel	VARCHAR(10),
	
	PRIMARY KEY(CompanyName, IConName),
	FOREIGN KEY(IConID) REFERENCES InternshipApp_Users(Identifier)
);

CREATE TABLE Project(
	ProjectName	VARCHAR(30),
	Description	TEXT,
	Progress	TEXT,
	Time		TEXT,
	Studentqualities TEXT,
	Topic		VARCHAR(127),
	Internship	INT(1),
	SupID	VARCHAR(50),
	IConID VARCHAR(50),
	CompanyName	VARCHAR(30),
	IConName VARCHAR(30),
	PropAccept VARCHAR(30),
	StartPro VARCHAR(30),
	MidRev VARCHAR(30),
	ThesisSub VARCHAR(30),
	ThesisAcc VARCHAR(30),
	PresSched VARCHAR(30),
	
	PRIMARY KEY(ProjectName),
	FOREIGN KEY(SupID) REFERENCES Supervisor(SupID),
	FOREIGN KEY(IConID) REFERENCES Internship_Contact(IConID),
	FOREIGN KEY(CompanyName, IConName) REFERENCES Internship_Contact(CompanyName, IConName)
);

CREATE TABLE Internship_of(
	ProjectName	VARCHAR(30),
	LocName	VARCHAR(30),
	Location		VARCHAR(30),
	StreetNr	VARCHAR(30),
	Travel		INT(1),
	Tnotes		VARCHAR(30),
	Pay		VARCHAR(30),
	CompanyName	VARCHAR(30), 
	
	PRIMARY KEY(ProjectName),
	FOREIGN KEY(ProjectName) REFERENCES Project(ProjectName),
	FOREIGN KEY(CompanyName) REFERENCES Internship_Contact(CompanyName) ON DELETE CASCADE
);

CREATE TABLE RelationOptions(
    type VARCHAR(20),
    
    PRIMARY KEY (type)
);

CREATE TABLE Supervises(
    type        VARCHAR(20),	
    SupID	VARCHAR(50),
    StuID	VARCHAR(30),
    Accepted	INT(1),
    ActivationCode	VARCHAR(32) UNIQUE,
    DateRequested	 VARCHAR(30),
    DateAccepted	VARCHAR(30),
    DateTerminated	 VARCHAR(30),

 	
    PRIMARY KEY (type, StuID, DateRequested),
    FOREIGN KEY(type) REFERENCES RelationOptions(type),
    FOREIGN KEY(SupID) REFERENCES Supervisor(SupID),
    FOREIGN KEY(StuID) REFERENCES Student(StuID)
);

CREATE TABLE Does(
	StuID	VARCHAR(30),
	ProjectName	VARCHAR(30),
	Accepted	INT(1),
	ActivationCode	VARCHAR(32) UNIQUE,
	DateRequested	 VARCHAR(30),	
	DateAccepted	VARCHAR(30),	
	DateTerminated	 VARCHAR(30),

	PRIMARY KEY(StuID, ProjectName, DateRequested),
	FOREIGN KEY(StuID) REFERENCES Student(StuID),
	FOREIGN KEY(ProjectName) REFERENCES Project(ProjectName)
);

CREATE TABLE Part_of(
	ProjectName	VARCHAR(30),
	CompanyName	VARCHAR(30),
	
	PRIMARY KEY(ProjectName, CompanyName),
	FOREIGN KEY(ProjectName) REFERENCES Project(ProjectName),
	FOREIGN KEY(CompanyName) REFERENCES Internship_Contact(CompanyName)
);

CREATE TABLE Log(
	StuID	VARCHAR(30),
	Date	VARCHAR(30),
	Entry	TEXT,
	
	PRIMARY KEY(StuID, Date),
	FOREIGN KEY(StuID) REFERENCES Student(StuID)	
);

