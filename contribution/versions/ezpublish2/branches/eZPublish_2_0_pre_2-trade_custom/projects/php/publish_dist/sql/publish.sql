# MySQL dump 8.12
#
# Host: localhost    Database: publish
#--------------------------------------------------------
# Server version	3.23.32

#
# Table structure for table 'eZAd_Ad'
#

DROP TABLE IF EXISTS eZAd_Ad;
CREATE TABLE eZAd_Ad (
  ID int(11) NOT NULL auto_increment,
  Name varchar(150) default NULL,
  ImageID int(11) default NULL,
  ViewStartDate timestamp(14) NOT NULL,
  ViewStopDate timestamp(14) NOT NULL,
  ViewRule enum('Period','Click') default 'Click',
  URL varchar(200) default NULL,
  Description text,
  IsActive enum('true','false') default NULL,
  ViewPrice float(10,2) default NULL,
  ClickPrice float(10,2) default NULL,
  HTMLBanner text NOT NULL,
  UseHTML int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAd_AdCategoryLink'
#

DROP TABLE IF EXISTS eZAd_AdCategoryLink;
CREATE TABLE eZAd_AdCategoryLink (
  ID int(11) NOT NULL auto_increment,
  CategoryID int(11) default NULL,
  AdID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAd_Category'
#

DROP TABLE IF EXISTS eZAd_Category;
CREATE TABLE eZAd_Category (
  ID int(11) NOT NULL auto_increment,
  Name varchar(150) default NULL,
  Description text,
  ParentID int(11) default NULL,
  ExcludeFromSearch enum('true','false') default 'false',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAd_Click'
#

DROP TABLE IF EXISTS eZAd_Click;
CREATE TABLE eZAd_Click (
  ID int(11) NOT NULL auto_increment,
  AdID int(11) default NULL,
  PageViewID int(11) default NULL,
  ClickPrice float(10,2) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZAd_View'
#

DROP TABLE IF EXISTS eZAd_View;
CREATE TABLE eZAd_View (
  ID int(11) NOT NULL auto_increment,
  AdID int(11) default NULL,
  Date date default NULL,
  ViewCount int(11) NOT NULL default '0',
  ViewPrice int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_Address'
#

DROP TABLE IF EXISTS eZAddress_Address;
CREATE TABLE eZAddress_Address (
  ID int(11) NOT NULL auto_increment,
  Street1 char(50) default NULL,
  Street2 char(50) default NULL,
  AddressTypeID int(11) default NULL,
  Place char(50) default NULL,
  Zip char(10) default NULL,
  CountryID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_AddressDefinition'
#

DROP TABLE IF EXISTS eZAddress_AddressDefinition;
CREATE TABLE eZAddress_AddressDefinition (
  UserID int(11) NOT NULL default '0',
  AddressID int(11) NOT NULL default '0',
  PRIMARY KEY (UserID,AddressID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_AddressType'
#

DROP TABLE IF EXISTS eZAddress_AddressType;
CREATE TABLE eZAddress_AddressType (
  ID int(11) NOT NULL auto_increment,
  Name char(50) default NULL,
  ListOrder int(11) NOT NULL default '0',
  Removed int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_Country'
#

DROP TABLE IF EXISTS eZAddress_Country;
CREATE TABLE eZAddress_Country (
  ID int(11) NOT NULL auto_increment,
  ISO char(2) default NULL,
  Name char(100) default NULL,
  Removed int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'eZAddress_Country'
#

INSERT INTO eZAddress_Country VALUES (2,'AF','Afghanistan',0);
INSERT INTO eZAddress_Country VALUES (3,'AL','Albania',0);
INSERT INTO eZAddress_Country VALUES (4,'DZ','Algeria',0);
INSERT INTO eZAddress_Country VALUES (5,'AS','American Samoa',0);
INSERT INTO eZAddress_Country VALUES (6,'AD','Andorra',0);
INSERT INTO eZAddress_Country VALUES (7,'AO','Angola',0);
INSERT INTO eZAddress_Country VALUES (8,'AI','Anguilla',0);
INSERT INTO eZAddress_Country VALUES (9,'AQ','Antarctica',0);
INSERT INTO eZAddress_Country VALUES (10,'AG','Antigua and Barbuda',0);
INSERT INTO eZAddress_Country VALUES (11,'AR','Argentina',0);
INSERT INTO eZAddress_Country VALUES (12,'AM','Armenia',0);
INSERT INTO eZAddress_Country VALUES (13,'AW','Aruba',0);
INSERT INTO eZAddress_Country VALUES (14,'AU','Australia',0);
INSERT INTO eZAddress_Country VALUES (15,'AT','Austria',0);
INSERT INTO eZAddress_Country VALUES (16,'AZ','Azerbaijan',0);
INSERT INTO eZAddress_Country VALUES (17,'BS','Bahamas',0);
INSERT INTO eZAddress_Country VALUES (18,'BH','Bahrain',0);
INSERT INTO eZAddress_Country VALUES (19,'BD','Bangladesh',0);
INSERT INTO eZAddress_Country VALUES (20,'BB','Barbados',0);
INSERT INTO eZAddress_Country VALUES (21,'BY','Belarus',0);
INSERT INTO eZAddress_Country VALUES (22,'BE','Belgium',0);
INSERT INTO eZAddress_Country VALUES (23,'BZ','Belize',0);
INSERT INTO eZAddress_Country VALUES (24,'BJ','Benin',0);
INSERT INTO eZAddress_Country VALUES (25,'BM','Bermuda',0);
INSERT INTO eZAddress_Country VALUES (26,'BT','Bhutan',0);
INSERT INTO eZAddress_Country VALUES (27,'BO','Bolivia',0);
INSERT INTO eZAddress_Country VALUES (28,'BA','Bosnia and Herzegovina',0);
INSERT INTO eZAddress_Country VALUES (29,'BW','Botswana',0);
INSERT INTO eZAddress_Country VALUES (30,'BV','Bouvet Island',0);
INSERT INTO eZAddress_Country VALUES (31,'BR','Brazil',0);
INSERT INTO eZAddress_Country VALUES (32,'IO','British Indian Ocean Territory',0);
INSERT INTO eZAddress_Country VALUES (33,'BN','Brunei Darussalam',0);
INSERT INTO eZAddress_Country VALUES (34,'BG','Bulgaria',0);
INSERT INTO eZAddress_Country VALUES (35,'BF','Burkina Faso',0);
INSERT INTO eZAddress_Country VALUES (36,'BI','Burundi',0);
INSERT INTO eZAddress_Country VALUES (37,'KH','Cambodia',0);
INSERT INTO eZAddress_Country VALUES (38,'CM','Cameroon',0);
INSERT INTO eZAddress_Country VALUES (39,'CA','Canada',0);
INSERT INTO eZAddress_Country VALUES (40,'CV','Cape Verde',0);
INSERT INTO eZAddress_Country VALUES (41,'KY','Cayman Islands',0);
INSERT INTO eZAddress_Country VALUES (42,'CF','Central African Republic',0);
INSERT INTO eZAddress_Country VALUES (43,'TD','Chad',0);
INSERT INTO eZAddress_Country VALUES (44,'CL','Chile',0);
INSERT INTO eZAddress_Country VALUES (45,'CN','China',0);
INSERT INTO eZAddress_Country VALUES (46,'CX','Christmas Island',0);
INSERT INTO eZAddress_Country VALUES (47,'CC','Cocos (Keeling) Islands',0);
INSERT INTO eZAddress_Country VALUES (48,'CO','Colombia',0);
INSERT INTO eZAddress_Country VALUES (49,'KM','Comoros',0);
INSERT INTO eZAddress_Country VALUES (50,'CG','Congo',0);
INSERT INTO eZAddress_Country VALUES (51,'CK','Cook Islands',0);
INSERT INTO eZAddress_Country VALUES (52,'CR','Costa Rica',0);
INSERT INTO eZAddress_Country VALUES (53,'CI','Cote d\'Ivoire',0);
INSERT INTO eZAddress_Country VALUES (54,'HR','Croatia',0);
INSERT INTO eZAddress_Country VALUES (55,'CU','Cuba',0);
INSERT INTO eZAddress_Country VALUES (56,'CY','Cyprus',0);
INSERT INTO eZAddress_Country VALUES (57,'CZ','Czech Republic',0);
INSERT INTO eZAddress_Country VALUES (58,'DK','Denmark',0);
INSERT INTO eZAddress_Country VALUES (59,'DJ','Djibouti',0);
INSERT INTO eZAddress_Country VALUES (60,'DM','Dominica',0);
INSERT INTO eZAddress_Country VALUES (61,'DO','Dominican Republic',0);
INSERT INTO eZAddress_Country VALUES (62,'TP','East Timor',0);
INSERT INTO eZAddress_Country VALUES (63,'EC','Ecuador',0);
INSERT INTO eZAddress_Country VALUES (64,'EG','Egypt',0);
INSERT INTO eZAddress_Country VALUES (65,'SV','El Salvador',0);
INSERT INTO eZAddress_Country VALUES (66,'GQ','Equatorial Guinea',0);
INSERT INTO eZAddress_Country VALUES (67,'ER','Eritrea',0);
INSERT INTO eZAddress_Country VALUES (68,'EE','Estonia',0);
INSERT INTO eZAddress_Country VALUES (69,'ET','Ethiopia',0);
INSERT INTO eZAddress_Country VALUES (70,'FK','Falkland Islands (Malvinas)',0);
INSERT INTO eZAddress_Country VALUES (71,'FO','Faroe Islands',0);
INSERT INTO eZAddress_Country VALUES (72,'FJ','Fiji',0);
INSERT INTO eZAddress_Country VALUES (73,'FI','Finland',0);
INSERT INTO eZAddress_Country VALUES (74,'FR','France',0);
INSERT INTO eZAddress_Country VALUES (75,'FX','France, Metropolitan',0);
INSERT INTO eZAddress_Country VALUES (76,'GF','French Guiana',0);
INSERT INTO eZAddress_Country VALUES (77,'PF','French Polynesia',0);
INSERT INTO eZAddress_Country VALUES (78,'TF','French Southern Territories',0);
INSERT INTO eZAddress_Country VALUES (79,'GA','Gabon',0);
INSERT INTO eZAddress_Country VALUES (80,'GM','Gambia',0);
INSERT INTO eZAddress_Country VALUES (81,'GE','Georgia',0);
INSERT INTO eZAddress_Country VALUES (82,'DE','Germany',0);
INSERT INTO eZAddress_Country VALUES (83,'GH','Ghana',0);
INSERT INTO eZAddress_Country VALUES (84,'GI','Gibraltar',0);
INSERT INTO eZAddress_Country VALUES (85,'GR','Greece',0);
INSERT INTO eZAddress_Country VALUES (86,'GL','Greenland',0);
INSERT INTO eZAddress_Country VALUES (87,'GD','Grenada',0);
INSERT INTO eZAddress_Country VALUES (88,'GP','Guadeloupe',0);
INSERT INTO eZAddress_Country VALUES (89,'GU','Guam',0);
INSERT INTO eZAddress_Country VALUES (90,'GT','Guatemala',0);
INSERT INTO eZAddress_Country VALUES (91,'GN','Guinea',0);
INSERT INTO eZAddress_Country VALUES (92,'GW','Guinea-Bissau',0);
INSERT INTO eZAddress_Country VALUES (93,'GY','Guyana',0);
INSERT INTO eZAddress_Country VALUES (94,'HT','Haiti',0);
INSERT INTO eZAddress_Country VALUES (95,'HM','Heard Island and McDonald Islands',0);
INSERT INTO eZAddress_Country VALUES (96,'HN','Honduras',0);
INSERT INTO eZAddress_Country VALUES (97,'HK','Hong Kong',0);
INSERT INTO eZAddress_Country VALUES (98,'HU','Hungary',0);
INSERT INTO eZAddress_Country VALUES (99,'IS','Iceland',0);
INSERT INTO eZAddress_Country VALUES (100,'IN','India',0);
INSERT INTO eZAddress_Country VALUES (101,'ID','Indonesia',0);
INSERT INTO eZAddress_Country VALUES (102,'IR','Iran (Islamic Republic of)',0);
INSERT INTO eZAddress_Country VALUES (103,'IQ','Iraq',0);
INSERT INTO eZAddress_Country VALUES (104,'IE','Ireland',0);
INSERT INTO eZAddress_Country VALUES (105,'IL','Israel',0);
INSERT INTO eZAddress_Country VALUES (106,'IT','Italy',0);
INSERT INTO eZAddress_Country VALUES (107,'JM','Jamaica',0);
INSERT INTO eZAddress_Country VALUES (108,'JP','Japan',0);
INSERT INTO eZAddress_Country VALUES (109,'JO','Jordan',0);
INSERT INTO eZAddress_Country VALUES (110,'KZ','Kazakhstan',0);
INSERT INTO eZAddress_Country VALUES (111,'KE','Kenya',0);
INSERT INTO eZAddress_Country VALUES (112,'KI','Kiribati',0);
INSERT INTO eZAddress_Country VALUES (113,'KP','Korea, Democratic People\'s Republic of',0);
INSERT INTO eZAddress_Country VALUES (114,'KR','Korea, Republic of',0);
INSERT INTO eZAddress_Country VALUES (115,'KW','Kuwait',0);
INSERT INTO eZAddress_Country VALUES (116,'KG','Kyrgyzstan',0);
INSERT INTO eZAddress_Country VALUES (117,'LA','Lao People\'s Democratic Republic',0);
INSERT INTO eZAddress_Country VALUES (118,'LT','Latin America',0);
INSERT INTO eZAddress_Country VALUES (119,'LV','Latvia',0);
INSERT INTO eZAddress_Country VALUES (120,'LB','Lebanon',0);
INSERT INTO eZAddress_Country VALUES (121,'LS','Lesotho',0);
INSERT INTO eZAddress_Country VALUES (122,'LR','Liberia',0);
INSERT INTO eZAddress_Country VALUES (123,'LY','Libyan Arab Jamahiriya',0);
INSERT INTO eZAddress_Country VALUES (124,'LI','Liechtenstein',0);
INSERT INTO eZAddress_Country VALUES (125,'LX','Lithuania',0);
INSERT INTO eZAddress_Country VALUES (126,'LU','Luxembourg',0);
INSERT INTO eZAddress_Country VALUES (127,'MO','Macau',0);
INSERT INTO eZAddress_Country VALUES (128,'MK','Macedonia',0);
INSERT INTO eZAddress_Country VALUES (129,'MG','Madagascar',0);
INSERT INTO eZAddress_Country VALUES (130,'MW','Malawi',0);
INSERT INTO eZAddress_Country VALUES (131,'MY','Malaysia',0);
INSERT INTO eZAddress_Country VALUES (132,'MV','Maldives',0);
INSERT INTO eZAddress_Country VALUES (133,'ML','Mali',0);
INSERT INTO eZAddress_Country VALUES (134,'MT','Malta',0);
INSERT INTO eZAddress_Country VALUES (135,'MH','Marshall Islands',0);
INSERT INTO eZAddress_Country VALUES (136,'MQ','Martinique',0);
INSERT INTO eZAddress_Country VALUES (137,'MR','Mauritania',0);
INSERT INTO eZAddress_Country VALUES (138,'MU','Mauritius',0);
INSERT INTO eZAddress_Country VALUES (139,'YT','Mayotte',0);
INSERT INTO eZAddress_Country VALUES (140,'MX','Mexico',0);
INSERT INTO eZAddress_Country VALUES (141,'FM','Micronesia (Federated States of)',0);
INSERT INTO eZAddress_Country VALUES (142,'MD','Moldova, Republic of',0);
INSERT INTO eZAddress_Country VALUES (143,'MC','Monaco',0);
INSERT INTO eZAddress_Country VALUES (144,'MN','Mongolia',0);
INSERT INTO eZAddress_Country VALUES (145,'MS','Montserrat',0);
INSERT INTO eZAddress_Country VALUES (146,'MA','Morocco',0);
INSERT INTO eZAddress_Country VALUES (147,'MZ','Mozambique',0);
INSERT INTO eZAddress_Country VALUES (148,'MM','Myanmar',0);
INSERT INTO eZAddress_Country VALUES (149,'NA','Namibia',0);
INSERT INTO eZAddress_Country VALUES (150,'NR','Nauru',0);
INSERT INTO eZAddress_Country VALUES (151,'NP','Nepal',0);
INSERT INTO eZAddress_Country VALUES (152,'NL','Netherlands',0);
INSERT INTO eZAddress_Country VALUES (153,'AN','Netherlands Antilles',0);
INSERT INTO eZAddress_Country VALUES (154,'NC','New Caledonia',0);
INSERT INTO eZAddress_Country VALUES (155,'NZ','New Zealand',0);
INSERT INTO eZAddress_Country VALUES (156,'NI','Nicaragua',0);
INSERT INTO eZAddress_Country VALUES (157,'NE','Niger',0);
INSERT INTO eZAddress_Country VALUES (158,'NG','Nigeria',0);
INSERT INTO eZAddress_Country VALUES (159,'NU','Niue',0);
INSERT INTO eZAddress_Country VALUES (160,'NF','Norfolk Island',0);
INSERT INTO eZAddress_Country VALUES (161,'MP','Northern Mariana Islands',0);
INSERT INTO eZAddress_Country VALUES (162,'NO','Norway',0);
INSERT INTO eZAddress_Country VALUES (163,'OM','Oman',0);
INSERT INTO eZAddress_Country VALUES (164,'PK','Pakistan',0);
INSERT INTO eZAddress_Country VALUES (165,'PW','Palau',0);
INSERT INTO eZAddress_Country VALUES (166,'PA','Panama',0);
INSERT INTO eZAddress_Country VALUES (167,'PG','Papua New Guinea',0);
INSERT INTO eZAddress_Country VALUES (168,'PY','Paraguay',0);
INSERT INTO eZAddress_Country VALUES (169,'PE','Peru',0);
INSERT INTO eZAddress_Country VALUES (170,'PH','Philippines',0);
INSERT INTO eZAddress_Country VALUES (171,'PN','Pitcairn',0);
INSERT INTO eZAddress_Country VALUES (172,'PL','Poland',0);
INSERT INTO eZAddress_Country VALUES (173,'PT','Portugal',0);
INSERT INTO eZAddress_Country VALUES (174,'PR','Puerto Rico',0);
INSERT INTO eZAddress_Country VALUES (175,'QA','Qatar',0);
INSERT INTO eZAddress_Country VALUES (176,'RE','Reunion',0);
INSERT INTO eZAddress_Country VALUES (177,'RO','Romania',0);
INSERT INTO eZAddress_Country VALUES (178,'RU','Russian Federation',0);
INSERT INTO eZAddress_Country VALUES (179,'RW','Rwanda',0);
INSERT INTO eZAddress_Country VALUES (180,'SH','Saint Helena',0);
INSERT INTO eZAddress_Country VALUES (181,'KN','Saint Kitts and Nevis',0);
INSERT INTO eZAddress_Country VALUES (182,'LC','Saint Lucia',0);
INSERT INTO eZAddress_Country VALUES (183,'PM','Saint Pierre and Miquelon',0);
INSERT INTO eZAddress_Country VALUES (184,'VC','Saint Vincent and the Grenadines',0);
INSERT INTO eZAddress_Country VALUES (185,'WS','Samoa',0);
INSERT INTO eZAddress_Country VALUES (186,'SM','San Marino',0);
INSERT INTO eZAddress_Country VALUES (187,'ST','Sao Tome and Principe',0);
INSERT INTO eZAddress_Country VALUES (188,'SA','Saudi Arabia',0);
INSERT INTO eZAddress_Country VALUES (189,'SN','Senegal',0);
INSERT INTO eZAddress_Country VALUES (190,'SC','Seychelles',0);
INSERT INTO eZAddress_Country VALUES (191,'SL','Sierra Leone',0);
INSERT INTO eZAddress_Country VALUES (192,'SG','Singapore',0);
INSERT INTO eZAddress_Country VALUES (193,'SK','Slovakia',0);
INSERT INTO eZAddress_Country VALUES (194,'SI','Slovenia',0);
INSERT INTO eZAddress_Country VALUES (195,'SB','Solomon Islands',0);
INSERT INTO eZAddress_Country VALUES (196,'SO','Somalia',0);
INSERT INTO eZAddress_Country VALUES (197,'ZA','South Africa',0);
INSERT INTO eZAddress_Country VALUES (198,'GS','South Georgia and the South Sandwich Island',0);
INSERT INTO eZAddress_Country VALUES (199,'ES','Spain',0);
INSERT INTO eZAddress_Country VALUES (200,'LK','Sri Lanka',0);
INSERT INTO eZAddress_Country VALUES (201,'SD','Sudan',0);
INSERT INTO eZAddress_Country VALUES (202,'SR','Suriname',0);
INSERT INTO eZAddress_Country VALUES (203,'SJ','Svalbard and Jan Mayen Islands',0);
INSERT INTO eZAddress_Country VALUES (204,'SZ','Swaziland',0);
INSERT INTO eZAddress_Country VALUES (205,'SE','Sweden',0);
INSERT INTO eZAddress_Country VALUES (206,'CH','Switzerland',0);
INSERT INTO eZAddress_Country VALUES (207,'SY','Syrian Arab Republic',0);
INSERT INTO eZAddress_Country VALUES (208,'TW','Taiwan, Republic of China',0);
INSERT INTO eZAddress_Country VALUES (209,'TJ','Tajikistan',0);
INSERT INTO eZAddress_Country VALUES (210,'TZ','Tanzania, United Republic of',0);
INSERT INTO eZAddress_Country VALUES (211,'TH','Thailand',0);
INSERT INTO eZAddress_Country VALUES (212,'TG','Togo',0);
INSERT INTO eZAddress_Country VALUES (213,'TK','Tokelau',0);
INSERT INTO eZAddress_Country VALUES (214,'TO','Tonga',0);
INSERT INTO eZAddress_Country VALUES (215,'TT','Trinidad and Tobago',0);
INSERT INTO eZAddress_Country VALUES (216,'TN','Tunisia',0);
INSERT INTO eZAddress_Country VALUES (217,'TR','Turkey',0);
INSERT INTO eZAddress_Country VALUES (218,'TM','Turkmenistan',0);
INSERT INTO eZAddress_Country VALUES (219,'TC','Turks and Caicos Islands',0);
INSERT INTO eZAddress_Country VALUES (220,'TV','Tuvalu',0);
INSERT INTO eZAddress_Country VALUES (221,'UG','Uganda',0);
INSERT INTO eZAddress_Country VALUES (222,'UA','Ukraine',0);
INSERT INTO eZAddress_Country VALUES (223,'AE','United Arab Emirates',0);
INSERT INTO eZAddress_Country VALUES (224,'GB','United Kingdom',0);
INSERT INTO eZAddress_Country VALUES (225,'UM','United States Minor Outlying Islands',0);
INSERT INTO eZAddress_Country VALUES (226,'UY','Uruguay',0);
INSERT INTO eZAddress_Country VALUES (227,'UZ','Uzbekistan',0);
INSERT INTO eZAddress_Country VALUES (228,'VU','Vanuatu',0);
INSERT INTO eZAddress_Country VALUES (229,'VA','Vatican City State (Holy See)',0);
INSERT INTO eZAddress_Country VALUES (230,'VE','Venezuela',0);
INSERT INTO eZAddress_Country VALUES (231,'VN','Viet Nam',0);
INSERT INTO eZAddress_Country VALUES (232,'VG','Virgin Islands (British)',0);
INSERT INTO eZAddress_Country VALUES (233,'VI','Virgin Islands (U.S.)',0);
INSERT INTO eZAddress_Country VALUES (234,'WF','Wallis and Futuna Islands',0);
INSERT INTO eZAddress_Country VALUES (235,'EH','Western Sahara',0);
INSERT INTO eZAddress_Country VALUES (236,'YE','Yemen',0);
INSERT INTO eZAddress_Country VALUES (237,'YU','Yugoslavia',0);
INSERT INTO eZAddress_Country VALUES (238,'ZR','Zaire',0);
INSERT INTO eZAddress_Country VALUES (239,'ZM','Zambia',0);
INSERT INTO eZAddress_Country VALUES (240,'US','United States of America',0);

#
# Table structure for table 'eZAddress_Online'
#

DROP TABLE IF EXISTS eZAddress_Online;
CREATE TABLE eZAddress_Online (
  ID int(11) NOT NULL auto_increment,
  URL char(255) default NULL,
  OnlineTypeID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_OnlineType'
#

DROP TABLE IF EXISTS eZAddress_OnlineType;
CREATE TABLE eZAddress_OnlineType (
  ID int(11) NOT NULL auto_increment,
  Name varchar(50) default NULL,
  ListOrder int(11) NOT NULL default '0',
  URLPrefix varchar(30) NOT NULL default '',
  PrefixLink int(1) NOT NULL default '0',
  PrefixVisual int(1) NOT NULL default '0',
  Removed int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_Phone'
#

DROP TABLE IF EXISTS eZAddress_Phone;
CREATE TABLE eZAddress_Phone (
  ID int(11) NOT NULL auto_increment,
  Number varchar(22) default NULL,
  PhoneTypeID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZAddress_PhoneType'
#

DROP TABLE IF EXISTS eZAddress_PhoneType;
CREATE TABLE eZAddress_PhoneType (
  ID int(11) NOT NULL auto_increment,
  Name char(50) default NULL,
  ListOrder int(11) NOT NULL default '0',
  Removed int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_Article'
#

DROP TABLE IF EXISTS eZArticle_Article;
CREATE TABLE eZArticle_Article (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Contents text,
  AuthorText varchar(100) default NULL,
  LinkText varchar(50) default NULL,
  AuthorID int(11) NOT NULL default '0',
  Modified timestamp(14) NOT NULL,
  Created timestamp(14) NOT NULL,
  PageCount int(11) default NULL,
  IsPublished enum('true','false') default 'false',
  Published timestamp(14) NOT NULL,
  Keywords text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticleCategoryDefinition'
#

DROP TABLE IF EXISTS eZArticle_ArticleCategoryDefinition;
CREATE TABLE eZArticle_ArticleCategoryDefinition (
  ID int(11) NOT NULL auto_increment,
  ArticleID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticleCategoryLink'
#

DROP TABLE IF EXISTS eZArticle_ArticleCategoryLink;
CREATE TABLE eZArticle_ArticleCategoryLink (
  ID int(11) NOT NULL auto_increment,
  ArticleID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  Placement int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticleFileLink'
#

DROP TABLE IF EXISTS eZArticle_ArticleFileLink;
CREATE TABLE eZArticle_ArticleFileLink (
  ID int(11) NOT NULL auto_increment,
  ArticleID int(11) NOT NULL default '0',
  FileID int(11) NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticleForumLink'
#

DROP TABLE IF EXISTS eZArticle_ArticleForumLink;
CREATE TABLE eZArticle_ArticleForumLink (
  ID int(11) NOT NULL auto_increment,
  ArticleID int(11) NOT NULL default '0',
  ForumID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticleImageDefinition'
#

DROP TABLE IF EXISTS eZArticle_ArticleImageDefinition;
CREATE TABLE eZArticle_ArticleImageDefinition (
  ArticleID int(11) NOT NULL default '0',
  ThumbnailImageID int(11) default NULL,
  PRIMARY KEY (ArticleID),
  UNIQUE KEY ArticleID(ArticleID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticleImageLink'
#

DROP TABLE IF EXISTS eZArticle_ArticleImageLink;
CREATE TABLE eZArticle_ArticleImageLink (
  ID int(11) NOT NULL auto_increment,
  ArticleID int(11) NOT NULL default '0',
  ImageID int(11) NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_ArticlePermission'
#

DROP TABLE IF EXISTS eZArticle_ArticlePermission;
CREATE TABLE eZArticle_ArticlePermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_Category'
#

DROP TABLE IF EXISTS eZArticle_Category;
CREATE TABLE eZArticle_Category (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Description text,
  ParentID int(11) default '0',
  ExcludeFromSearch enum('true','false') default 'false',
  SortMode int(11) NOT NULL default '1',
  OwnerID int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_CategoryPermission'
#

DROP TABLE IF EXISTS eZArticle_CategoryPermission;
CREATE TABLE eZArticle_CategoryPermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZArticle_CategoryReaderLink'
#

DROP TABLE IF EXISTS eZArticle_CategoryReaderLink;
CREATE TABLE eZArticle_CategoryReaderLink (
  ID int(11) NOT NULL auto_increment,
  CategoryID int(11) NOT NULL default '0',
  GroupID int(11) NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZBug_Bug'
#

DROP TABLE IF EXISTS eZBug_Bug;
CREATE TABLE eZBug_Bug (
  ID int(11) NOT NULL auto_increment,
  Name varchar(150) default NULL,
  Description text,
  UserID int(11) NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  IsHandled enum('true','false') NOT NULL default 'false',
  PriorityID int(11) NOT NULL default '0',
  StatusID int(11) NOT NULL default '0',
  IsClosed enum('true','false') default 'false',
  UserEmail varchar(100) default '',
  OwnerID int(11) default NULL,
  IsPrivate enum('true','false') default 'false',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_BugCategoryLink'
#

DROP TABLE IF EXISTS eZBug_BugCategoryLink;
CREATE TABLE eZBug_BugCategoryLink (
  ID int(11) NOT NULL auto_increment,
  CategoryID int(11) default NULL,
  BugID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_BugFileLink'
#

DROP TABLE IF EXISTS eZBug_BugFileLink;
CREATE TABLE eZBug_BugFileLink (
  ID int(11) NOT NULL auto_increment,
  BugID int(11) NOT NULL default '0',
  FileID int(11) NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZBug_BugImageLink'
#

DROP TABLE IF EXISTS eZBug_BugImageLink;
CREATE TABLE eZBug_BugImageLink (
  ID int(11) NOT NULL auto_increment,
  BugID int(11) NOT NULL default '0',
  ImageID int(11) NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZBug_BugModuleLink'
#

DROP TABLE IF EXISTS eZBug_BugModuleLink;
CREATE TABLE eZBug_BugModuleLink (
  ID int(11) NOT NULL auto_increment,
  ModuleID int(11) default NULL,
  BugID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_Category'
#

DROP TABLE IF EXISTS eZBug_Category;
CREATE TABLE eZBug_Category (
  ID int(11) NOT NULL auto_increment,
  Name varchar(150) default NULL,
  Description text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_Log'
#

DROP TABLE IF EXISTS eZBug_Log;
CREATE TABLE eZBug_Log (
  ID int(11) NOT NULL auto_increment,
  BugID int(11) NOT NULL default '0',
  UserID int(11) NOT NULL default '0',
  Description text,
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_Module'
#

DROP TABLE IF EXISTS eZBug_Module;
CREATE TABLE eZBug_Module (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) default NULL,
  Name varchar(150) default NULL,
  Description text,
  OwnerGroupID int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_ModulePermission'
#

DROP TABLE IF EXISTS eZBug_ModulePermission;
CREATE TABLE eZBug_ModulePermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_Priority'
#

DROP TABLE IF EXISTS eZBug_Priority;
CREATE TABLE eZBug_Priority (
  ID int(11) NOT NULL auto_increment,
  Name char(150) NOT NULL default '',
  Value int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZBug_Status'
#

DROP TABLE IF EXISTS eZBug_Status;
CREATE TABLE eZBug_Status (
  ID int(11) NOT NULL auto_increment,
  Name char(150) NOT NULL default '',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZCalendar_Appointment'
#

DROP TABLE IF EXISTS eZCalendar_Appointment;
CREATE TABLE eZCalendar_Appointment (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  Date timestamp(14) NOT NULL,
  Duration time default NULL,
  AppointmentTypeID int(11) NOT NULL default '0',
  EMailNotice int(11) default '0',
  IsPrivate int(11) default NULL,
  Name varchar(200) default NULL,
  Description text,
  Priority int(11) NOT NULL default '1',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZCalendar_AppointmentType'
#

DROP TABLE IF EXISTS eZCalendar_AppointmentType;
CREATE TABLE eZCalendar_AppointmentType (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  Description text,
  Name varchar(200) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_Company'
#

DROP TABLE IF EXISTS eZContact_Company;
CREATE TABLE eZContact_Company (
  ID int(11) NOT NULL auto_increment,
  CreatorID int(11) NOT NULL default '0',
  Name varchar(50) NOT NULL default '',
  Comment text,
  ContactType int(1) NOT NULL default '0',
  CompanyNo varchar(20) NOT NULL default '',
  ContactID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyAddressDict'
#

DROP TABLE IF EXISTS eZContact_CompanyAddressDict;
CREATE TABLE eZContact_CompanyAddressDict (
  CompanyID int(11) NOT NULL default '0',
  AddressID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyID,AddressID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyImageDefinition'
#

DROP TABLE IF EXISTS eZContact_CompanyImageDefinition;
CREATE TABLE eZContact_CompanyImageDefinition (
  CompanyID int(11) NOT NULL default '0',
  CompanyImageID int(11) NOT NULL default '0',
  LogoImageID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyOnlineDict'
#

DROP TABLE IF EXISTS eZContact_CompanyOnlineDict;
CREATE TABLE eZContact_CompanyOnlineDict (
  CompanyID int(11) NOT NULL default '0',
  OnlineID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyID,OnlineID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyPersonDict'
#

DROP TABLE IF EXISTS eZContact_CompanyPersonDict;
CREATE TABLE eZContact_CompanyPersonDict (
  CompanyID int(11) NOT NULL default '0',
  PersonID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyID,PersonID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyPhoneDict'
#

DROP TABLE IF EXISTS eZContact_CompanyPhoneDict;
CREATE TABLE eZContact_CompanyPhoneDict (
  CompanyID int(11) NOT NULL default '0',
  PhoneID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyID,PhoneID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyProjectDict'
#

DROP TABLE IF EXISTS eZContact_CompanyProjectDict;
CREATE TABLE eZContact_CompanyProjectDict (
  CompanyID int(11) NOT NULL default '0',
  ProjectID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyID,ProjectID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyType'
#

DROP TABLE IF EXISTS eZContact_CompanyType;
CREATE TABLE eZContact_CompanyType (
  ID int(11) NOT NULL auto_increment,
  Name varchar(50) NOT NULL default '',
  Description text,
  ParentID int(11) NOT NULL default '0',
  ImageID int(11) NOT NULL default '0',
  PRIMARY KEY (ID),
  KEY ParentID(ParentID),
  KEY Name(Name)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_CompanyTypeDict'
#

DROP TABLE IF EXISTS eZContact_CompanyTypeDict;
CREATE TABLE eZContact_CompanyTypeDict (
  CompanyTypeID int(11) NOT NULL default '0',
  CompanyID int(11) NOT NULL default '0',
  PRIMARY KEY (CompanyTypeID,CompanyID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_ConsulationCompanyDict'
#

DROP TABLE IF EXISTS eZContact_ConsulationCompanyDict;
CREATE TABLE eZContact_ConsulationCompanyDict (
  ConsultationID int(11) NOT NULL default '0',
  CompanyID int(11) NOT NULL default '0',
  PRIMARY KEY (ConsultationID,CompanyID)
) TYPE=ISAM PACK_KEYS=1;

#
# Table structure for table 'eZContact_Consultation'
#

DROP TABLE IF EXISTS eZContact_Consultation;
CREATE TABLE eZContact_Consultation (
  ID int(11) NOT NULL auto_increment,
  ShortDesc varchar(100) NOT NULL default '',
  Description text NOT NULL,
  Date datetime NOT NULL default '0000-00-00 00:00:00',
  StateID int(11) NOT NULL default '0',
  EmailNotifications varchar(255) NOT NULL default '',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_ConsultationCompanyUserDict'
#

DROP TABLE IF EXISTS eZContact_ConsultationCompanyUserDict;
CREATE TABLE eZContact_ConsultationCompanyUserDict (
  ConsultationID int(11) NOT NULL default '0',
  CompanyID int(11) NOT NULL default '0',
  UserID int(11) NOT NULL default '0',
  PRIMARY KEY (ConsultationID,CompanyID,UserID)
) TYPE=MyISAM;


#
# Table structure for table 'eZContact_ConsultationGroupsDict'
#

DROP TABLE IF EXISTS eZContact_ConsultationGroupsDict;
CREATE TABLE eZContact_ConsultationGroupsDict (
  ConsultationID int(11) NOT NULL default '0',
  GroupID int(11) NOT NULL default '0',
  PRIMARY KEY (ConsultationID,GroupID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_ConsultationPersonUserDict'
#

DROP TABLE IF EXISTS eZContact_ConsultationPersonUserDict;
CREATE TABLE eZContact_ConsultationPersonUserDict (
  ConsultationID int(11) NOT NULL default '0',
  PersonID int(11) NOT NULL default '0',
  UserID int(11) NOT NULL default '0',
  PRIMARY KEY (ConsultationID,PersonID,UserID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_ConsultationType'
#

DROP TABLE IF EXISTS eZContact_ConsultationType;
CREATE TABLE eZContact_ConsultationType (
  ID int(11) NOT NULL auto_increment,
  Name varchar(50) default NULL,
  ListOrder int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_ContactType'
#

DROP TABLE IF EXISTS eZContact_ContactType;
CREATE TABLE eZContact_ContactType (
  ID int(11) NOT NULL auto_increment,
  Name varchar(50) NOT NULL default '',
  Description text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZContact_ImageType'
#

DROP TABLE IF EXISTS eZContact_ImageType;
CREATE TABLE eZContact_ImageType (
  ID int(11) NOT NULL auto_increment,
  Name char(50) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_Person'
#

DROP TABLE IF EXISTS eZContact_Person;
CREATE TABLE eZContact_Person (
  ID int(11) NOT NULL auto_increment,
  FirstName varchar(50) default NULL,
  LastName varchar(50) default NULL,
  BirthDate date default NULL,
  Comment text,
  ContactTypeID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_PersonAddressDict'
#

DROP TABLE IF EXISTS eZContact_PersonAddressDict;
CREATE TABLE eZContact_PersonAddressDict (
  PersonID int(11) NOT NULL default '0',
  AddressID int(11) NOT NULL default '0',
  PRIMARY KEY (PersonID,AddressID)
) TYPE=MyISAM;


#
# Table structure for table 'eZContact_PersonOnlineDict'
#

DROP TABLE IF EXISTS eZContact_PersonOnlineDict;
CREATE TABLE eZContact_PersonOnlineDict (
  PersonID int(11) NOT NULL default '0',
  OnlineID int(11) NOT NULL default '0',
  PRIMARY KEY (PersonID,OnlineID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_PersonPhoneDict'
#

DROP TABLE IF EXISTS eZContact_PersonPhoneDict;
CREATE TABLE eZContact_PersonPhoneDict (
  PersonID int(11) NOT NULL default '0',
  PhoneID int(11) NOT NULL default '0',
  PRIMARY KEY (PersonID,PhoneID)
) TYPE=MyISAM;


#
# Table structure for table 'eZContact_PersonProjectDict'
#

DROP TABLE IF EXISTS eZContact_PersonProjectDict;
CREATE TABLE eZContact_PersonProjectDict (
  PersonID int(11) NOT NULL default '0',
  ProjectID int(11) NOT NULL default '0',
  PRIMARY KEY (PersonID,ProjectID)
) TYPE=MyISAM;


#
# Table structure for table 'eZContact_ProjectType'
#

DROP TABLE IF EXISTS eZContact_ProjectType;
CREATE TABLE eZContact_ProjectType (
  ID int(11) NOT NULL auto_increment,
  Name char(50) NOT NULL default '',
  ListOrder int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_UserCompanyDict'
#

DROP TABLE IF EXISTS eZContact_UserCompanyDict;
CREATE TABLE eZContact_UserCompanyDict (
  UserID int(11) NOT NULL default '0',
  CompanyID int(11) NOT NULL default '0',
  PRIMARY KEY (UserID,CompanyID),
  UNIQUE KEY CompanyID(CompanyID),
  UNIQUE KEY UserID(UserID)
) TYPE=MyISAM;

#
# Table structure for table 'eZContact_UserPersonDict'
#

DROP TABLE IF EXISTS eZContact_UserPersonDict;
CREATE TABLE eZContact_UserPersonDict (
  UserID int(11) NOT NULL default '0',
  PersonID int(11) NOT NULL default '0',
  PRIMARY KEY (UserID,PersonID),
  UNIQUE KEY PersonID(PersonID),
  UNIQUE KEY UserID(UserID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_File'
#

DROP TABLE IF EXISTS eZFileManager_File;
CREATE TABLE eZFileManager_File (
  ID int(11) NOT NULL auto_increment,
  Name char(200) default NULL,
  Description char(200) default NULL,
  FileName char(200) default NULL,
  OriginalFileName char(200) default NULL,
  ReadPermission int(11) default '1',
  WritePermission int(11) default '1',
  UserID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FileFolderLink'
#

DROP TABLE IF EXISTS eZFileManager_FileFolderLink;
CREATE TABLE eZFileManager_FileFolderLink (
  ID int(11) NOT NULL auto_increment,
  FolderID int(11) NOT NULL default '0',
  FileID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FilePageViewLink'
#

DROP TABLE IF EXISTS eZFileManager_FilePageViewLink;
CREATE TABLE eZFileManager_FilePageViewLink (
  ID int(11) NOT NULL auto_increment,
  PageViewID int(11) NOT NULL default '0',
  FileID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FilePermission'
#

DROP TABLE IF EXISTS eZFileManager_FilePermission;
CREATE TABLE eZFileManager_FilePermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FileReadGroupLink'
#

DROP TABLE IF EXISTS eZFileManager_FileReadGroupLink;
CREATE TABLE eZFileManager_FileReadGroupLink (
  ID int(11) NOT NULL auto_increment,
  GroupID int(11) default NULL,
  FileID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FileWriteGroupLink'
#

DROP TABLE IF EXISTS eZFileManager_FileWriteGroupLink;
CREATE TABLE eZFileManager_FileWriteGroupLink (
  ID int(11) NOT NULL auto_increment,
  GroupID int(11) default NULL,
  FileID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_Folder'
#

DROP TABLE IF EXISTS eZFileManager_Folder;
CREATE TABLE eZFileManager_Folder (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Description text,
  ParentID int(11) NOT NULL default '0',
  ReadPermission int(11) default '1',
  WritePermission int(11) default '1',
  UserID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FolderPermission'
#

DROP TABLE IF EXISTS eZFileManager_FolderPermission;
CREATE TABLE eZFileManager_FolderPermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FolderReadGroupLink'
#

DROP TABLE IF EXISTS eZFileManager_FolderReadGroupLink;
CREATE TABLE eZFileManager_FolderReadGroupLink (
  ID int(11) NOT NULL auto_increment,
  GroupID int(11) default NULL,
  FolderID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZFileManager_FolderWriteGroupLink'
#

DROP TABLE IF EXISTS eZFileManager_FolderWriteGroupLink;
CREATE TABLE eZFileManager_FolderWriteGroupLink (
  ID int(11) NOT NULL auto_increment,
  GroupID int(11) default NULL,
  FolderID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZForum_Category'
#

DROP TABLE IF EXISTS eZForum_Category;
CREATE TABLE eZForum_Category (
  Name varchar(20) default NULL,
  Description varchar(40) default NULL,
  IsPrivate int(1) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZForum_Forum'
#

DROP TABLE IF EXISTS eZForum_Forum;
CREATE TABLE eZForum_Forum (
  Name varchar(20) NOT NULL default '',
  Description varchar(40) default NULL,
  IsPrivate int(1) default NULL,
  ID int(11) NOT NULL auto_increment,
  ModeratorID int(11) NOT NULL default '0',
  IsModerated int(1) NOT NULL default '0',
  GroupID int(11) default '0',
  IsAnonymous int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZForum_ForumCategoryLink'
#

DROP TABLE IF EXISTS eZForum_ForumCategoryLink;
CREATE TABLE eZForum_ForumCategoryLink (
  ID int(11) NOT NULL auto_increment,
  ForumID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZForum_Message'
#

DROP TABLE IF EXISTS eZForum_Message;
CREATE TABLE eZForum_Message (
  ForumID int(11) NOT NULL default '0',
  Topic varchar(60) default NULL,
  Body text,
  UserID int(11) default NULL,
  Parent int(11) default NULL,
  EmailNotice int(1) NOT NULL default '0',
  PostingTime timestamp(14) NOT NULL,
  TreeID int(11) default NULL,
  ThreadID int(11) default NULL,
  Depth int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  IsApproved int(1) NOT NULL default '1',
  IsTemporary int(1) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_Category'
#

DROP TABLE IF EXISTS eZImageCatalogue_Category;
CREATE TABLE eZImageCatalogue_Category (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Description text,
  ParentID int(11) default NULL,
  UserID int(11) default NULL,
  WritePermission int(11) default '1',
  ReadPermission int(11) default '1',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_CategoryPermission'
#

DROP TABLE IF EXISTS eZImageCatalogue_CategoryPermission;
CREATE TABLE eZImageCatalogue_CategoryPermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_Image'
#

DROP TABLE IF EXISTS eZImageCatalogue_Image;
CREATE TABLE eZImageCatalogue_Image (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Caption text,
  Description text,
  FileName varchar(100) default NULL,
  OriginalFileName varchar(100) default NULL,
  ReadPermission int(11) default '1',
  WritePermission int(11) default '1',
  UserID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_ImageCategoryLink'
#

DROP TABLE IF EXISTS eZImageCatalogue_ImageCategoryLink;
CREATE TABLE eZImageCatalogue_ImageCategoryLink (
  ID int(11) NOT NULL auto_increment,
  CategoryID int(11) default NULL,
  ImageID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_ImagePermission'
#

DROP TABLE IF EXISTS eZImageCatalogue_ImagePermission;
CREATE TABLE eZImageCatalogue_ImagePermission (
  ID int(11) NOT NULL auto_increment,
  ObjectID int(11) default NULL,
  GroupID int(11) default NULL,
  ReadPermission int(11) default '0',
  WritePermission int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_ImageVariation'
#

DROP TABLE IF EXISTS eZImageCatalogue_ImageVariation;
CREATE TABLE eZImageCatalogue_ImageVariation (
  ID int(11) NOT NULL auto_increment,
  ImageID int(11) default NULL,
  VariationGroupID int(11) default NULL,
  ImagePath char(100) default NULL,
  Width int(11) default NULL,
  Height int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZImageCatalogue_ImageVariationGroup'
#

DROP TABLE IF EXISTS eZImageCatalogue_ImageVariationGroup;
CREATE TABLE eZImageCatalogue_ImageVariationGroup (
  ID int(11) NOT NULL auto_increment,
  Width int(11) default NULL,
  Height int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZLink_Category'
#

DROP TABLE IF EXISTS eZLink_Category;
CREATE TABLE eZLink_Category (
  ID int(11) NOT NULL auto_increment,
  Parent int(11) default '0',
  Name char(100) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZLink_Hit'
#

DROP TABLE IF EXISTS eZLink_Hit;
CREATE TABLE eZLink_Hit (
  ID int(11) NOT NULL auto_increment,
  Link int(11) default NULL,
  Time timestamp(14) NOT NULL,
  RemoteIP char(15) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZLink_Link'
#

DROP TABLE IF EXISTS eZLink_Link;
CREATE TABLE eZLink_Link (
  ID int(11) NOT NULL auto_increment,
  Title varchar(100) default NULL,
  Description text,
  LinkGroup int(11) default NULL,
  KeyWords varchar(100) default NULL,
  Modified timestamp(14) NOT NULL,
  Accepted enum('Y','N') default NULL,
  Created datetime default NULL,
  Url varchar(100) default NULL,
  ImageID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZLink_LinkCategoryDefinition'
#

DROP TABLE IF EXISTS eZLink_LinkCategoryDefinition;
CREATE TABLE eZLink_LinkCategoryDefinition (
  ID int(11) NOT NULL auto_increment,
  LinkID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZLink_LinkCategoryLink'
#

DROP TABLE IF EXISTS eZLink_LinkCategoryLink;
CREATE TABLE eZLink_LinkCategoryLink (
  ID int(11) NOT NULL auto_increment,
  LinkID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZLink_LinkGroup'
#

DROP TABLE IF EXISTS eZLink_LinkGroup;
CREATE TABLE eZLink_LinkGroup (
  ID int(11) NOT NULL auto_increment,
  Parent int(11) default '0',
  Title varchar(100) default NULL,
  ImageID int(11) default NULL,
  Description text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZNewsFeed_Category'
#

DROP TABLE IF EXISTS eZNewsFeed_Category;
CREATE TABLE eZNewsFeed_Category (
  ID int(11) NOT NULL auto_increment,
  Name varchar(150) NOT NULL default '',
  Description text,
  ParentID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZNewsFeed_News'
#

DROP TABLE IF EXISTS eZNewsFeed_News;
CREATE TABLE eZNewsFeed_News (
  ID int(11) NOT NULL auto_increment,
  IsPublished enum('true','false') default 'false',
  PublishingDate timestamp(14) NOT NULL,
  OriginalPublishingDate timestamp(14) NOT NULL,
  Name varchar(150) NOT NULL default '',
  Intro text,
  KeyWords varchar(200) default NULL,
  URL varchar(200) default NULL,
  Origin varchar(150) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZNewsFeed_NewsCategoryLink'
#

DROP TABLE IF EXISTS eZNewsFeed_NewsCategoryLink;
CREATE TABLE eZNewsFeed_NewsCategoryLink (
  ID int(11) NOT NULL auto_increment,
  NewsID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZNewsFeed_SourceSite'
#

DROP TABLE IF EXISTS eZNewsFeed_SourceSite;
CREATE TABLE eZNewsFeed_SourceSite (
  ID int(11) NOT NULL auto_increment,
  URL char(250) default NULL,
  Login char(30) default NULL,
  Password char(30) default NULL,
  CategoryID int(11) NOT NULL default '0',
  Name char(100) default NULL,
  Decoder char(50) default NULL,
  IsActive enum('true','false') default 'false',
  AutoPublish int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZPoll_MainPoll'
#

DROP TABLE IF EXISTS eZPoll_MainPoll;
CREATE TABLE eZPoll_MainPoll (
  ID int(11) NOT NULL auto_increment,
  PollID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZPoll_Poll'
#

DROP TABLE IF EXISTS eZPoll_Poll;
CREATE TABLE eZPoll_Poll (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Description text,
  Percent enum('true','false') default NULL,
  Number enum('true','false') default NULL,
  Anonymous enum('true','false') default NULL,
  IsEnabled enum('true','false') default 'false',
  IsClosed enum('true','false') default 'false',
  ShowResult enum('true','false') default 'false',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZPoll_PollChoice'
#

DROP TABLE IF EXISTS eZPoll_PollChoice;
CREATE TABLE eZPoll_PollChoice (
  ID int(11) NOT NULL auto_increment,
  PollID int(11) default NULL,
  Name char(100) default NULL,
  Offset int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZPoll_Vote'
#

DROP TABLE IF EXISTS eZPoll_Vote;
CREATE TABLE eZPoll_Vote (
  ID int(11) NOT NULL auto_increment,
  PollID int(11) default NULL,
  ChoiceID int(11) default NULL,
  VotingIP char(20) default NULL,
  UserID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZSession_Preferences'
#

DROP TABLE IF EXISTS eZSession_Preferences;
CREATE TABLE eZSession_Preferences (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  Name char(50) default NULL,
  Value char(255) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZSession_Session'
#

DROP TABLE IF EXISTS eZSession_Session;
CREATE TABLE eZSession_Session (
  ID int(11) NOT NULL auto_increment,
  Hash char(33) default NULL,
  Created timestamp(14) NOT NULL,
  LastAccessed timestamp(14) NOT NULL,
  SecondLastAccessed timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZSession_SessionVariable'
#

DROP TABLE IF EXISTS eZSession_SessionVariable;
CREATE TABLE eZSession_SessionVariable (
  ID int(11) NOT NULL auto_increment,
  SessionID int(11) default NULL,
  Name char(25) default NULL,
  Value char(50) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZStats_BrowserType'
#

DROP TABLE IF EXISTS eZStats_BrowserType;
CREATE TABLE eZStats_BrowserType (
  ID int(11) NOT NULL auto_increment,
  BrowserType char(250) NOT NULL default '',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZStats_PageView'
#

DROP TABLE IF EXISTS eZStats_PageView;
CREATE TABLE eZStats_PageView (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  BrowserTypeID int(11) NOT NULL default '0',
  RemoteHostID int(11) NOT NULL default '0',
  RefererURLID int(11) NOT NULL default '0',
  Date timestamp(14) NOT NULL,
  RequestPageID int(11) NOT NULL default '0',
  DateValue date NOT NULL default '0000-00-00',
  TimeValue time NOT NULL default '00:00:00',
  PRIMARY KEY (ID),
  KEY DateValue(DateValue),
  KEY TimeValue(TimeValue),
  KEY Date(Date)
) TYPE=MyISAM;

#
# Table structure for table 'eZStats_RefererURL'
#

DROP TABLE IF EXISTS eZStats_RefererURL;
CREATE TABLE eZStats_RefererURL (
  ID int(11) NOT NULL auto_increment,
  Domain char(100) default NULL,
  URI char(200) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZStats_RemoteHost'
#

DROP TABLE IF EXISTS eZStats_RemoteHost;
CREATE TABLE eZStats_RemoteHost (
  ID int(11) NOT NULL auto_increment,
  IP char(15) default NULL,
  HostName char(150) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZStats_RequestPage'
#

DROP TABLE IF EXISTS eZStats_RequestPage;
CREATE TABLE eZStats_RequestPage (
  ID int(11) NOT NULL auto_increment,
  URI char(250) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTodo_Category'
#

DROP TABLE IF EXISTS eZTodo_Category;
CREATE TABLE eZTodo_Category (
  Description text,
  ID int(11) NOT NULL auto_increment,
  Name varchar(30) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTodo_Priority'
#

DROP TABLE IF EXISTS eZTodo_Priority;
CREATE TABLE eZTodo_Priority (
  ID int(11) NOT NULL auto_increment,
  Name varchar(30) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTodo_Todo'
#

DROP TABLE IF EXISTS eZTodo_Todo;
CREATE TABLE eZTodo_Todo (
  Category int(11) default NULL,
  Priority int(11) default NULL,
  Permission enum('Public','Private') default 'Private',
  ID int(11) NOT NULL auto_increment,
  UserID int(11) default NULL,
  OwnerID int(11) default NULL,
  Name varchar(30) default NULL,
  Date timestamp(14) NOT NULL,
  Due timestamp(14) NOT NULL,
  Description text,
  Status int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_AlternativeCurrency'
#

DROP TABLE IF EXISTS eZTrade_AlternativeCurrency;
CREATE TABLE eZTrade_AlternativeCurrency (
  ID int(11) NOT NULL auto_increment,
  Name char(100) NOT NULL default '',
  PrefixSign int(11) NOT NULL default '0',
  Sign char(5) NOT NULL default '',
  Value float NOT NULL default '1',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_Attribute'
#

DROP TABLE IF EXISTS eZTrade_Attribute;
CREATE TABLE eZTrade_Attribute (
  ID int(11) NOT NULL auto_increment,
  TypeID int(11) default NULL,
  Name char(150) default NULL,
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_AttributeValue'
#

DROP TABLE IF EXISTS eZTrade_AttributeValue;
CREATE TABLE eZTrade_AttributeValue (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) default NULL,
  AttributeID int(11) default NULL,
  Value char(200) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_Cart'
#

DROP TABLE IF EXISTS eZTrade_Cart;
CREATE TABLE eZTrade_Cart (
  ID int(11) NOT NULL auto_increment,
  SessionID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_CartItem'
#

DROP TABLE IF EXISTS eZTrade_CartItem;
CREATE TABLE eZTrade_CartItem (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) default NULL,
  Count int(11) default NULL,
  CartID int(11) default NULL,
  WishListItemID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_CartOptionValue'
#

DROP TABLE IF EXISTS eZTrade_CartOptionValue;
CREATE TABLE eZTrade_CartOptionValue (
  ID int(11) NOT NULL auto_increment,
  CartItemID int(11) default NULL,
  OptionID int(11) default NULL,
  OptionValueID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_Category'
#

DROP TABLE IF EXISTS eZTrade_Category;
CREATE TABLE eZTrade_Category (
  ID int(11) NOT NULL auto_increment,
  Parent int(11) default NULL,
  Description text,
  Name varchar(100) default NULL,
  ImageID int(11) default NULL,
  SortMode int(11) NOT NULL default '1',
  RemoteID varchar(100) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_CategoryOptionLink'
#

DROP TABLE IF EXISTS eZTrade_CategoryOptionLink;
CREATE TABLE eZTrade_CategoryOptionLink (
  ID int(11) NOT NULL auto_increment,
  CategoryID int(11) default NULL,
  OptionID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_GroupPriceLink'
#

DROP TABLE IF EXISTS eZTrade_GroupPriceLink;
CREATE TABLE eZTrade_GroupPriceLink (
  GroupID int(11) NOT NULL default '0',
  PriceID int(11) NOT NULL default '0',
  PRIMARY KEY (GroupID,PriceID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_Option'
#

DROP TABLE IF EXISTS eZTrade_Option;
CREATE TABLE eZTrade_Option (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Description text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_OptionValue'
#

DROP TABLE IF EXISTS eZTrade_OptionValue;
CREATE TABLE eZTrade_OptionValue (
  ID int(11) NOT NULL auto_increment,
  OptionID int(11) default NULL,
  Placement int(11) NOT NULL default '1',
  Price float(10,2) NOT NULL default '0.00',
  RemoteID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_OptionValueContent'
#

DROP TABLE IF EXISTS eZTrade_OptionValueContent;
CREATE TABLE eZTrade_OptionValueContent (
  ID int(11) NOT NULL auto_increment,
  Value varchar(30) default NULL,
  ValueID int(11) NOT NULL default '0',
  Placement int(11) NOT NULL default '1',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_OptionValueHeader'
#

DROP TABLE IF EXISTS eZTrade_OptionValueHeader;
CREATE TABLE eZTrade_OptionValueHeader (
  ID int(11) NOT NULL auto_increment,
  Name varchar(30) default NULL,
  OptionID int(11) NOT NULL default '0',
  Placement int(11) NOT NULL default '1',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_Order'
#

DROP TABLE IF EXISTS eZTrade_Order;
CREATE TABLE eZTrade_Order (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  ShippingCharge float(10,2) default NULL,
  PaymentMethod text,
  ShippingAddressID int(11) default NULL,
  BillingAddressID int(11) default NULL,
  IsExported int(11) NOT NULL default '0',
  Date datetime default NULL,
  IsActive int(11) default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_OrderItem'
#

DROP TABLE IF EXISTS eZTrade_OrderItem;
CREATE TABLE eZTrade_OrderItem (
  ID int(11) NOT NULL auto_increment,
  OrderID int(11) NOT NULL default '0',
  Count int(11) default NULL,
  Price float(10,2) default NULL,
  ProductID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_OrderOptionValue'
#

DROP TABLE IF EXISTS eZTrade_OrderOptionValue;
CREATE TABLE eZTrade_OrderOptionValue (
  ID int(11) NOT NULL auto_increment,
  OrderItemID int(11) default NULL,
  OptionName char(25) default NULL,
  ValueName char(25) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_OrderStatus'
#

DROP TABLE IF EXISTS eZTrade_OrderStatus;
CREATE TABLE eZTrade_OrderStatus (
  ID int(11) NOT NULL auto_increment,
  StatusID int(11) NOT NULL default '0',
  Altered timestamp(14) NOT NULL,
  AdminID int(11) default NULL,
  OrderID int(11) NOT NULL default '0',
  Comment text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_OrderStatusType'
#

DROP TABLE IF EXISTS eZTrade_OrderStatusType;
CREATE TABLE eZTrade_OrderStatusType (
  ID int(11) NOT NULL auto_increment,
  Name char(25) NOT NULL default '',
  PRIMARY KEY (ID),
  UNIQUE KEY Name(Name)
) TYPE=MyISAM;



#
# Dumping data for table 'eZTrade_OrderStatusType'
#

INSERT INTO eZTrade_OrderStatusType VALUES (1,'intl-initial');
INSERT INTO eZTrade_OrderStatusType VALUES (2,'intl-sendt');
INSERT INTO eZTrade_OrderStatusType VALUES (3,'intl-payed');
INSERT INTO eZTrade_OrderStatusType VALUES (4,'intl-undefined');

#
# Table structure for table 'eZTrade_PriceGroup'
#

DROP TABLE IF EXISTS eZTrade_PriceGroup;
CREATE TABLE eZTrade_PriceGroup (
  ID int(11) NOT NULL auto_increment,
  Name varchar(50) default NULL,
  Description text,
  Placement int(11) NOT NULL default '1',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_Product'
#

DROP TABLE IF EXISTS eZTrade_Product;
CREATE TABLE eZTrade_Product (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Brief text,
  Description text,
  Keywords varchar(100) default NULL,
  Price float(10,2) default NULL,
  ShowPrice enum('true','false') default NULL,
  ShowProduct enum('true','false') default NULL,
  Discontinued enum('true','false') default NULL,
  InheritOptions enum('true','false') default NULL,
  ProductNumber varchar(100) default NULL,
  ExternalLink varchar(200) default NULL,
  IsHotDeal enum('true','false') default 'false',
  Published timestamp(14) NOT NULL,
  Altered timestamp(14) NOT NULL,
  RemoteID varchar(100) default NULL,
  VATTypeID int(11) NOT NULL default '0',
  ShippingGroupID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_ProductCategoryDefinition'
#

DROP TABLE IF EXISTS eZTrade_ProductCategoryDefinition;
CREATE TABLE eZTrade_ProductCategoryDefinition (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) NOT NULL default '0',
  CategoryID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ProductCategoryLink'
#

DROP TABLE IF EXISTS eZTrade_ProductCategoryLink;
CREATE TABLE eZTrade_ProductCategoryLink (
  ID int(11) NOT NULL auto_increment,
  CategoryID int(11) default NULL,
  ProductID int(11) default NULL,
  Placement int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ProductImageDefinition'
#

DROP TABLE IF EXISTS eZTrade_ProductImageDefinition;
CREATE TABLE eZTrade_ProductImageDefinition (
  ProductID int(11) NOT NULL default '0',
  ThumbnailImageID int(11) default NULL,
  MainImageID int(11) default NULL,
  PRIMARY KEY (ProductID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ProductImageLink'
#

DROP TABLE IF EXISTS eZTrade_ProductImageLink;
CREATE TABLE eZTrade_ProductImageLink (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) default NULL,
  ImageID int(11) default NULL,
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ProductOptionLink'
#

DROP TABLE IF EXISTS eZTrade_ProductOptionLink;
CREATE TABLE eZTrade_ProductOptionLink (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) default NULL,
  OptionID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ProductPriceLink'
#

DROP TABLE IF EXISTS eZTrade_ProductPriceLink;
CREATE TABLE eZTrade_ProductPriceLink (
  ProductID int(11) NOT NULL default '0',
  PriceID int(11) NOT NULL default '0',
  OptionID int(11) NOT NULL default '0',
  ValueID int(11) NOT NULL default '0',
  Price float(10,2) default NULL,
  PRIMARY KEY (ProductID,PriceID,OptionID,ValueID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_ProductTypeLink'
#

DROP TABLE IF EXISTS eZTrade_ProductTypeLink;
CREATE TABLE eZTrade_ProductTypeLink (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) default NULL,
  TypeID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ShippingGroup'
#

DROP TABLE IF EXISTS eZTrade_ShippingGroup;
CREATE TABLE eZTrade_ShippingGroup (
  ID int(11) NOT NULL auto_increment,
  Name char(100) default NULL,
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ShippingType'
#

DROP TABLE IF EXISTS eZTrade_ShippingType;
CREATE TABLE eZTrade_ShippingType (
  ID int(11) NOT NULL auto_increment,
  Name char(100) default NULL,
  Created timestamp(14) NOT NULL,
  IsDefault int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_ShippingValue'
#

DROP TABLE IF EXISTS eZTrade_ShippingValue;
CREATE TABLE eZTrade_ShippingValue (
  ID int(11) NOT NULL auto_increment,
  ShippingGroupID int(11) NOT NULL default '0',
  ShippingTypeID int(11) NOT NULL default '0',
  StartValue float NOT NULL default '0',
  AddValue float NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_Type'
#

DROP TABLE IF EXISTS eZTrade_Type;
CREATE TABLE eZTrade_Type (
  ID int(11) NOT NULL auto_increment,
  Name varchar(150) default NULL,
  Description text,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_VATType'
#

DROP TABLE IF EXISTS eZTrade_VATType;
CREATE TABLE eZTrade_VATType (
  ID int(11) NOT NULL auto_increment,
  Name char(100) default NULL,
  VATValue float NOT NULL default '0',
  Created timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_WishList'
#

DROP TABLE IF EXISTS eZTrade_WishList;
CREATE TABLE eZTrade_WishList (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) default NULL,
  IsPublic int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZTrade_WishListItem'
#

DROP TABLE IF EXISTS eZTrade_WishListItem;
CREATE TABLE eZTrade_WishListItem (
  ID int(11) NOT NULL auto_increment,
  ProductID int(11) default NULL,
  Count int(11) default NULL,
  WishListID int(11) default NULL,
  IsBought int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZTrade_WishListOptionValue'
#

DROP TABLE IF EXISTS eZTrade_WishListOptionValue;
CREATE TABLE eZTrade_WishListOptionValue (
  ID int(11) NOT NULL auto_increment,
  WishListItemID int(11) default NULL,
  OptionID int(11) default NULL,
  OptionValueID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZUser_Forgot'
#

DROP TABLE IF EXISTS eZUser_Forgot;
CREATE TABLE eZUser_Forgot (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  Hash char(33) default NULL,
  Time timestamp(14) NOT NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Table structure for table 'eZUser_Group'
#

DROP TABLE IF EXISTS eZUser_Group;
CREATE TABLE eZUser_Group (
  ID int(11) NOT NULL auto_increment,
  Name varchar(100) default NULL,
  Description text,
  SessionTimeout int(11) default '60',
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'eZUser_Group'
#

INSERT INTO eZUser_Group VALUES (1,'Administrators','All rights',7200);

#
# Table structure for table 'eZUser_GroupPermissionLink'
#

DROP TABLE IF EXISTS eZUser_GroupPermissionLink;
CREATE TABLE eZUser_GroupPermissionLink (
  ID int(11) NOT NULL auto_increment,
  GroupID int(11) default NULL,
  PermissionID int(11) default NULL,
  IsEnabled enum('true','false') default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

INSERT INTO eZUser_GroupPermissionLink VALUES (1,1,1,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (2,1,2,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (3,1,3,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (4,1,4,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (5,1,5,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (6,1,6,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (7,1,8,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (21,1,15,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (20,1,14,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (19,1,13,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (18,1,12,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (17,1,11,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (16,1,10,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (15,1,9,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (22,1,16,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (23,1,17,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (24,1,18,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (25,1,19,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (26,1,20,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (27,1,21,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (28,1,22,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (29,1,23,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (30,1,24,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (31,1,25,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (32,1,26,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (33,1,27,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (34,1,28,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (35,1,29,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (36,1,30,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (37,1,31,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (38,1,32,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (39,1,33,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (40,1,34,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (74,1,35,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (75,1,36,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (76,1,37,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (77,1,38,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (78,1,39,'true');
INSERT INTO eZUser_GroupPermissionLink VALUES (79,1,40,'true');


#
# Table structure for table 'eZUser_Module'
#

DROP TABLE IF EXISTS eZUser_Module;
CREATE TABLE eZUser_Module (
  ID int(11) NOT NULL auto_increment,
  Name char(100) NOT NULL default '',
  PRIMARY KEY (ID),
  UNIQUE KEY Name(Name)
) TYPE=MyISAM;

INSERT INTO eZUser_Module VALUES (1,'eZTrade');
INSERT INTO eZUser_Module VALUES (2,'eZPoll');
INSERT INTO eZUser_Module VALUES (3,'eZUser');
INSERT INTO eZUser_Module VALUES (4,'eZTodo');
INSERT INTO eZUser_Module VALUES (5,'eZNews');
INSERT INTO eZUser_Module VALUES (6,'eZContact');
INSERT INTO eZUser_Module VALUES (7,'eZForum');
INSERT INTO eZUser_Module VALUES (8,'eZLink');
INSERT INTO eZUser_Module VALUES (9,'eZFileManager');
INSERT INTO eZUser_Module VALUES (10,'eZImageCatalogue');
INSERT INTO eZUser_Module VALUES (11,'eZBug');

#
# Table structure for table 'eZUser_Permission'
#

DROP TABLE IF EXISTS eZUser_Permission;
CREATE TABLE eZUser_Permission (
  ID int(11) NOT NULL auto_increment,
  ModuleID int(11) default NULL,
  Name char(100) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'eZUser_Permission'
#

INSERT INTO eZUser_Permission VALUES (1,3,'UserAdd');
INSERT INTO eZUser_Permission VALUES (2,3,'UserDelete');
INSERT INTO eZUser_Permission VALUES (3,3,'UserModify');
INSERT INTO eZUser_Permission VALUES (4,3,'GroupDelete');
INSERT INTO eZUser_Permission VALUES (5,3,'GroupAdd');
INSERT INTO eZUser_Permission VALUES (6,3,'GroupModify');
INSERT INTO eZUser_Permission VALUES (11,8,'LinkGroupModify');
INSERT INTO eZUser_Permission VALUES (8,3,'AdminLogin');
INSERT INTO eZUser_Permission VALUES (10,8,'LinkGroupAdd');
INSERT INTO eZUser_Permission VALUES (9,8,'LinkGroupDelete');
INSERT INTO eZUser_Permission VALUES (12,8,'LinkModify');
INSERT INTO eZUser_Permission VALUES (13,8,'LinkAdd');
INSERT INTO eZUser_Permission VALUES (14,8,'LinkDelete');
INSERT INTO eZUser_Permission VALUES (15,7,'CategoryAdd');
INSERT INTO eZUser_Permission VALUES (16,7,'CategoryModify');
INSERT INTO eZUser_Permission VALUES (17,7,'CategoryDelete');
INSERT INTO eZUser_Permission VALUES (18,7,'ForumDelete');
INSERT INTO eZUser_Permission VALUES (19,7,'ForumAdd');
INSERT INTO eZUser_Permission VALUES (20,7,'ForumModify');
INSERT INTO eZUser_Permission VALUES (21,7,'MessageModify');
INSERT INTO eZUser_Permission VALUES (22,7,'MessageAdd');
INSERT INTO eZUser_Permission VALUES (23,7,'MessageDelete');
INSERT INTO eZUser_Permission VALUES (24,6,'PersonAdd');
INSERT INTO eZUser_Permission VALUES (25,6,'CompanyAdd');
INSERT INTO eZUser_Permission VALUES (26,6,'CategoryAdd');
INSERT INTO eZUser_Permission VALUES (27,6,'PersonDelete');
INSERT INTO eZUser_Permission VALUES (28,6,'CompanyDelete');
INSERT INTO eZUser_Permission VALUES (29,6,'CategoryDelete');
INSERT INTO eZUser_Permission VALUES (30,6,'PersonModify');
INSERT INTO eZUser_Permission VALUES (31,6,'CompanyModify');
INSERT INTO eZUser_Permission VALUES (32,6,'CategoryModify');
INSERT INTO eZUser_Permission VALUES (33,6,'PersonView');
INSERT INTO eZUser_Permission VALUES (34,6,'PersonList');
INSERT INTO eZUser_Permission VALUES (35,3,'UserLogin');
INSERT INTO eZUser_Permission VALUES (36,9,'WriteToRoot');
INSERT INTO eZUser_Permission VALUES (37,9,'WritePermission');
INSERT INTO eZUser_Permission VALUES (38,10,'WritePermission');
INSERT INTO eZUser_Permission VALUES (39,10,'WriteToRoot');
INSERT INTO eZUser_Permission VALUES (40,11,'BugEdit');
INSERT INTO eZUser_Permission VALUES (41,6,'CompanyView');
INSERT INTO eZUser_Permission VALUES (42,6,'CompanyList');
INSERT INTO eZUser_Permission VALUES (43,6,'TypeAdmin');
INSERT INTO eZUser_Permission VALUES (44,6,'Consultation');

#
# Table structure for table 'eZUser_User'
#

DROP TABLE IF EXISTS eZUser_User;
CREATE TABLE eZUser_User (
  ID int(11) NOT NULL auto_increment,
  Login varchar(50) NOT NULL default '',
  Password varchar(50) NOT NULL default '',
  Email varchar(50) default NULL,
  FirstName varchar(50) default NULL,
  LastName varchar(50) default NULL,
  InfoSubscription enum('true','false') default 'false',
  Signature text NOT NULL,
  PRIMARY KEY (ID),
  UNIQUE KEY Login(Login)
) TYPE=MyISAM;

#
# Dumping data for table 'eZUser_User'
#

INSERT INTO eZUser_User VALUES (1,'admin','0c947f956f7aa781','postmaster@yourdomain','admin','user','false','');

#
# Table structure for table 'eZUser_UserAddressLink'
#

DROP TABLE IF EXISTS eZUser_UserAddressLink;
CREATE TABLE eZUser_UserAddressLink (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) NOT NULL default '0',
  AddressID int(11) NOT NULL default '0',
  PRIMARY KEY (ID)
) TYPE=MyISAM;


#
# Table structure for table 'eZUser_UserGroupLink'
#

DROP TABLE IF EXISTS eZUser_UserGroupLink;
CREATE TABLE eZUser_UserGroupLink (
  ID int(11) NOT NULL auto_increment,
  UserID int(11) default NULL,
  GroupID int(11) default NULL,
  PRIMARY KEY (ID)
) TYPE=MyISAM;

#
# Dumping data for table 'eZUser_UserGroupLink'
#

INSERT INTO eZUser_UserGroupLink VALUES (1,1,1);
