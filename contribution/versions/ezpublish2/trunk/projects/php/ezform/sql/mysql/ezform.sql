CREATE TABLE eZForm_Form (
  ID int(11) NOT NULL,
  Name varchar(255) default NULL,
  Receiver varchar(255) default NULL,
  CC varchar(255) default NULL,
  Sender varchar(255) default NULL,
  SendAsUser varchar(1) default NULL,
  CompletedPage varchar(255) default NULL,
  InstructionPage varchar(255) default NULL,
  InstructionPageName varchar(255) default NULL,
  Counter int(11) default NULL,
  useDatabaseStorage varchar(1) default NULL,
  TitleField int(11),
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormCondition (
  ID int(11) NOT NULL,
  ElementID int(11) NOT NULL,
  PageID int(11) NOT NULL,
  Max int(11) default '0',
  Min int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormPage (
  ID int(11) NOT NULL,
  Name varchar(255) default NULL,
  FormID int(11) default '0',
  Placement int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormElement (
  ID int(11) NOT NULL,
  Name varchar(255) default NULL,
  Required int(1) default '0',
  ElementTypeID int(11) default NULL,
  Size int(11) default '0',
  Break int(11) default '0',
  Hide int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_PageElementDict (
  ID int(11) NOT NULL,
  Name varchar(255) default NULL,
  PageID int(11) default NULL,
  ElementID int(11) default NULL,
  Placement int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormElementType (
  ID int(11) NOT NULL,
  Name varchar(255) default NULL,
  Description text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormElementFixedValues (
  ID int(11) NOT NULL default '0',
  Value varchar(80) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;
 
CREATE TABLE eZForm_FormElementFixedValueLink (
  ID int(11) NOT NULL default '0',
  ElementID int(11) default '0',
  FixedValueID int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormTable (
  ID int(11) NOT NULL default '0',
  ElementID int(11) NOT NULL,
  Cols int(11) NOT NULL,
  Rows int(11) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormTableElementDict (
  ID int(11) NOT NULL default '0',
  Name varchar(255),
  TableID int(11),
  ElementID int(11),
  Placement int(11),
  PRIMARY KEY (ID)
) TYPE=MyISAM;    

CREATE TABLE eZForm_FormResults (
  ID int(11) NOT NULL default '0',
  UserHash char(33) NOT NULL,
  IsRegistered int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormElementResult (
  ID int(11) NOT NULL default '0',
  ElementID int(11),
  ResultID int(11),
  Result text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

CREATE TABLE eZForm_FormElementNumerical (
  ElementID int NOT NULL default '0',
  MaxValue varchar(10),
  MinValue varchar(10),
  PRIMARY KEY (ElementID)
) TYPE=MyISAM;    

CREATE TABLE eZForm_FormElementText (
  ElementID int(11) NOT NULL default '0',
  TextBlock text,
  PRIMARY KEY (ElementID)
) TYPE=MyISAM;    

INSERT INTO eZForm_FormElementType VALUES (1,'text_field_item','HTML text field (input type="text")');
INSERT INTO eZForm_FormElementType VALUES (2,'text_area_item','HTML text area (textarea)');
INSERT INTO eZForm_FormElementType VALUES (3,'dropdown_item','HTML Select');
INSERT INTO eZForm_FormElementType VALUES (4,'multiple_select_item','HTML Multiple Select');
INSERT INTO eZForm_FormElementType VALUES (5,'checkbox_item','HTML CheckBox');
INSERT INTO eZForm_FormElementType VALUES (6,'radiobox_item','HTML RadioBox');
INSERT INTO eZForm_FormElementType VALUES (7,'table_item','Table of elements');
INSERT INTO eZForm_FormElementType VALUES (8,'text_label_item','Text label');
INSERT INTO eZForm_FormElementType VALUES (9,'text_header_1_item','Header Level 1');
INSERT INTO eZForm_FormElementType VALUES (10,'text_header_2_item','Header Level 2');
INSERT INTO eZForm_FormElementType VALUES (11,'hr_line_item','Horizontal rule');
INSERT INTO eZForm_FormElementType VALUES (12,'numerical_float_item','Float value');
INSERT INTO eZForm_FormElementType VALUES (13,'numerical_integer_item','Integer value');
INSERT INTO eZForm_FormElementType VALUES (14,'text_block_item','Text');
INSERT INTO eZForm_FormElementType VALUES (15,'user_email_item','E-mail field for sender');
INSERT INTO eZForm_FormElementType VALUES (100,'empty_item','Nothing');

create index PageElementDict_PageID ON eZForm_PageElementDict (PageID);
create index PageElementDict_ElementID ON eZForm_PageElementDict (ElementID);
create index FormPage_FormID ON eZForm_FormPage (FormID);
create index FormTableElementDict_TableID ON eZForm_FormTableElementDict (TableID);
create index FormTableElementDict_ElementID ON eZForm_FormTableElementDict (ElementID);
create index FormTable_ElementID ON eZForm_FormTable (ElementID);
create index FormElementResult_ElementID ON eZForm_FormElementResult (ElementID);
create index FormElementResult_ResultID ON eZForm_FormElementResult (ResultID);

CREATE TABLE eZForm_FormReport (
  ID int(11) NOT NULL default '0',
  FormID int(11),
  Name varchar(255),
  PRIMARY KEY (ID)
) TYPE=MyISAM;


CREATE TABLE eZForm_FormReportElement (
  ID int(11) NOT NULL default '0',
  ElementID int(11),
  StatisticsType int(11),
  PRIMARY KEY (ID)
) TYPE=MyISAM;
