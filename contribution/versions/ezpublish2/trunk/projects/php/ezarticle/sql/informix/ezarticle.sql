
CREATE TABLE eZArticle_Article (
  ID int NOT NULL,
  Name varchar(100) NOT NULL,
  Contents text,
  ContentsWriterID int NOT NULL,
  LinkText varchar(50) NOT NULL,
  AuthorID int NOT NULL,
  Modified int NOT NULL,
  Created int NOT NULL,
  Published int NOT NULL,
  PageCount int NOT NULL,
  IsPublished int,
  Keywords lvarchar,
  Discuss int NOT NULL,
  TopicID int NOT NULL,
  StartDate int NOT NULL,
  StopDate int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleCategoryDefinition (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  CategoryID int NOT NULL,
  PRIMARY KEY (ID)
);


CREATE TABLE eZArticle_ArticleCategoryLink (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  CategoryID int NOT NULL,
  Placement int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleFileLink (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  FileID int NOT NULL,
  Created int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleFormDict (
  ID int NOT NULL,
  ArticleID int default NULL,
  FormID int default NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleForumLink (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  ForumID int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleImageDefinition (
  ArticleID int NOT NULL,
  ThumbnailImageID int default NULL,
  PRIMARY KEY (ArticleID)
);

CREATE TABLE eZArticle_ArticleImageLink (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  ImageID int NOT NULL,
  Created int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleKeyword (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  Keyword varchar(50) NOT NULL,
  Automatic int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticlePermission (
  ID int NOT NULL,
  ObjectID int default NULL,
  GroupID int default NULL,
  ReadPermission int,
  WritePermission int,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_ArticleTypeLink (
  ID int NOT NULL,
  ArticleID int default NULL,
  TypeID int default NULL,
  PRIMARY KEY (ID)
);


CREATE TABLE eZArticle_Attribute (
  ID int NOT NULL,
  TypeID int default NULL,
  Name char(150) default NULL,
  Placement int default NULL,
  Created int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_AttributeValue (
  ID int NOT NULL,
  ArticleID int default NULL,
  AttributeID int default NULL,
  Value lvarchar,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_BulkMailCategoryLink (
  ArticleCategoryID int NOT NULL,
  BulkMailCategoryID int NOT NULL,
  PRIMARY KEY (ArticleCategoryID,BulkMailCategoryID)
);

CREATE TABLE eZArticle_Category (
  ID int NOT NULL,
  Name varchar(100) default NULL,
  Description lvarchar,
  ParentID int,
  ExcludeFromSearch int,
  SortMode int NOT NULL,
  OwnerID int,
  Placement int,
  SectionID int NOT NULL,
  ImageID int default NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_CategoryPermission (
  ID int NOT NULL,
  ObjectID int default NULL,
  GroupID int default NULL,
  ReadPermission int,
  WritePermission int,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_CategoryReaderLink (
  ID int NOT NULL,
  CategoryID int NOT NULL,
  GroupID int NOT NULL,
  Created int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_Log (
  ID int NOT NULL,
  ArticleID int NOT NULL,
  Created int NOT NULL,
  Message lvarchar NOT NULL,
  UserID int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_Topic (
  ID int NOT NULL,
  Name varchar(255) default NULL,
  Description lvarchar,
  Created int NOT NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZArticle_Type (
  ID int NOT NULL,
  Name varchar(150) default NULL,
  PRIMARY KEY (ID)
);
