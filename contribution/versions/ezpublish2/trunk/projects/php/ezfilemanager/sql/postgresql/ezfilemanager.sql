CREATE TABLE eZFileManager_File (
  ID int NOT NULL,
  Name char(200),
  Description char(200),
  FileName char(200),
  OriginalFileName char(200),
  ReadPermission int DEFAULT '1',
  WritePermission int DEFAULT '1',
  UserID int,
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FileFolderLink (
  ID int NOT NULL,
  FolderID int NOT NULL,
  FileID int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FilePageViewLink (
  ID int NOT NULL,
  PageViewID int NOT NULL,
  FileID int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_Folder (
  ID int NOT NULL,
  Name char(100),
  Description text,
  ParentID int NOT NULL,
  ReadPermission int DEFAULT '1',
  WritePermission int DEFAULT '1',
  UserID int,
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FolderPermission (
  ID int NOT NULL,
  ObjectID int default NULL,
  GroupID int default NULL,
  ReadPermission int default '0',
  WritePermission int default '0',
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FilePermission (
  ID int NOT NULL,
  ObjectID int default NULL,
  GroupID int default NULL,
  ReadPermission int default '0',
  WritePermission int default '0',
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FileReadGroupLink (
  ID int NOT NULL,
  GroupID int default NULL,
  FileID int default NULL,
  PRIMARY KEY
);

CREATE TABLE eZFileManager_FolderReadGroupLink (
  ID int NOT NULL,
  GroupID int default NULL,
  FolderID int default NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FileReadGroupLink (
  ID int NOT NULL,
  GroupID int default NULL,
  FileID int default NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZFileManager_FileWriteGroupLink (
  ID int NOT NULL,
  GroupID int default NULL,
  FileID int default NULL,
  PRIMARY KEY (ID)
);
