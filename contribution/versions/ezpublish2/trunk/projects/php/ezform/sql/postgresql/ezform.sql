CREATE TABLE eZForm_Form (
  ID int NOT NULL,
  Name varchar(255) default NULL,
  Receiver varchar(255) default NULL,
  CC varchar(255) default NULL,
  Sender varchar(255) default NULL,
  SendAsUser varchar(1) default NULL,
  CompletedPage varchar(255) default NULL,
  InstructionPage varchar(255) default NULL,
  InstructionPageName varchar(255) default NULL,
  Counter int default NULL,
  useDatabaseStorage varchar(1) default NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZForm_FormElement (
  ID int NOT NULL,
  Name varchar(255) default NULL,
  Required int default '0',
  ElementTypeID int default NULL,
  Size int default '0',	
  Break int default '0',		
  PRIMARY KEY (ID)
);

CREATE TABLE eZForm_FormElementDict (
  ID int NOT NULL,
  Name varchar(255) default NULL,
  FormID int default NULL,
  ElementID int default NULL,
  Placement int default NULL,
  PRIMARY KEY (ID)
);

CREATE TABLE eZForm_FormElementType (
  ID int NOT NULL,
  Name varchar(255) default NULL,
  Description text,
  PRIMARY KEY (ID)
);

CREATE TABLE eZForm_FormElementFixedValues (
  ID int NOT NULL default '0',
  Value varchar(80) default NULL,
  PRIMARY KEY (ID)
);
 
CREATE TABLE eZForm_FormElementFixedValueLink (
  ID int NOT NULL default '0',
  ElementID int default '0',
  FixedValueID int default '0',
  PRIMARY KEY (ID)
);

CREATE TABLE eZForm_FormElementNumerical (
  ElementID int(11) NOT NULL default '0',
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

