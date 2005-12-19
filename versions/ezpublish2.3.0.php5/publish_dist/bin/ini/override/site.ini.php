<?php /*
#
# If you have a production site and a staging site with different settings
# you can create a directory called "override" in the main publish directory.
#
# In that directory you can have a completly different site.ini file which will
# be used instead of the correct one (this). This is great if the working
# site.ini is commited to CVS and you don't want to do changes to it.
#
# You can also create a file called site.ini.append in the override directory,
# that file will then be appended and override only those settings set in that
# file. This function can be used to select a staging database instead of the
# production database, but in all other parts use the correct site.ini settings.
#

[site]
SiteURL=example.org
AdminSiteURL=admin.example.org
AdminSiteProtocol=http
UserSiteURL=example.org
SiteDesign=standard
AdminDesign=ezpublish
SiteStyle=ezpublish
SiteTitle=eZ Community
Keywords=eZpublish
Language=en_GB

# Meta content variable
SiteAuthor=eZ Community
SiteCopyright=eZ Community &copy; 2004
SiteDescription=eZ publish - the web application suite
SiteKeywords=Content Management System, CMS, e-commerce

HelpLanguage=en_GB
SiteTmpDir=/tmp/

# Database settings set DatabaseImplementation to mysql|postgresql|informix|...
DatabaseImplementation=mysql
Server=localhost
Database=publish
User=publish
Password=publish
# If you need to specify the socket to use with mysql use this variable
MySQLSocket=disabled

# can be e.g. /article/view/42 or disabled
DefaultPage=/article/frontpage/1/

EnabledModules=eZArticle;eZTrade;eZForum;eZLink;eZPoll;eZAd;eZNewsfeed;eZBug;eZContact;eZTodo;eZCalendar;eZFileManager;eZImageCatalogue;eZMediaCatalogue;eZAddress;eZForm;eZBulkMail;eZMessage;eZQuiz;eZStats;eZURLTranslator;eZSiteManager;eZUser;eZSysInfo

ModuleList=eZArticle;eZTrade;eZForum;eZLink;eZPoll;eZAd;eZNewsfeed;eZBug;eZContact;eZTodo;eZCalendar;eZFileManager;eZImageCatalogue;eZMediaCatalogue;eZAddress;eZForm;eZBulkMail;eZMessage;eZQuiz;eZStats;eZURLTranslator;eZSiteManager;eZUser;eZSysInfo

URLTranslationKeyword=section-standard;section-intranet;section-trade;section-news

CacheHeaders=true
CheckDependence=enabled
LogDir=bin/logs/
LogFileName=error
DemoSite=disabled
ModuleTab=enabled
Sections=enabled
DefaultSection=1

# Site cache works only on simple sites  with no user specific data. E.g. an pure article site.
SiteCache=disabled
# How long before a cache times out, in minutes
SiteCacheTimeout=120

# Charsets for admin that can be used to display different languages
# You can leave this value blank to disble this option
CharsetSwitch=disabled
Charsets=en_US-English;en_GB-English;en_UC-Unicode;no_NO-Norwegian;ru_RU-Russian;lv_LV-Latvian;
#fr_FR-French;it_IT-Italian;de_DE-German;

[classes]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageConversionProgram=convert
DefaultSection=1


[eZAdMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=images/standard/
Language=en_GB
DefaultCategory=1
DefaultSection=1


[eZAddressMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
DocumentRoot=./ezaddress/
Language=en_GB
MaxCountryList=11
MaxRegionList=11
DefaultSection=1


[eZArticleMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
ThumbnailImageWidth=150
ThumbnailImageHeight=150
ThumbnailGroupImageWidth=150
ThumbnailGroupImageHeight=150
SmallImageWidth=100
SmallImageHeight=100
MediumImageWidth=200
MediumImageHeight=200
LargeImageWidth=300
LargeImageHeight=300
# size of images in the list in articleview
ListImageWidth=150
ListImageHeight=150

Language=en_GB
Generator=qdom
PageCaching=enabled
CapitalizeHeadlines=disabled
UserComments=enabled
DefaultLinkText=Read more
AdminListLimit=20
UserListLimit=20
PublishNoticeReceiver=nospam@example.org
PublishNoticeSender=nospam@example.org
PublishNoticePadding=3
AuthorLimit=10
AuthorArticleLimit=10
UserSubmitArticles=enabled
MixUnpublished=disabled
GrayScaleImageList=disabled
CanUserPublish=disabled
SearchListLimit=10
SearchWithinSections=disabled
CategoryImageWidth=50
CategoryImageHeight=50
DefaultSection=2
HeadlinesImageWidth=50
HeadlinesImageHeight=50
LowerCaseManualKeywords=enabled

# Add ability for fast edit inside the article, url translation,
# administrator can easy assign shortcuts for the articles
AdminURLTranslator=disabled

# Ability to use XML tags inside the category description, administrator have a possibility
# to format category description. This switch must be set before category creation. If you
# change this switch after some content created, YOU WILL LOOSE CATEGORIES !!!
# This feature is not currently supported in eZ publish desktop edition.
CategoryDescriptionXML=disabled

# if the article view should show the path of the categorydefinition even if linked from 
# other category
ForceCategoryDefinition=disabled

MailToFriendSender=nospam@example.org

# add extra tags here if you want to have your own custom tags in eZ publish
#
CustomTags=logo

# How often a word should be present to be ignored 0-1 (1==100%)
StopWordFrequency=0.7

[eZArticleRSS]
# Channel Title, Link, Description and Language
Title=eZ publish 2
Link=http://ezcommunity.net/
Description=News
Language=en_GB
# Channel Image
# Width should be 1-144, Height should be 1-400 (default: 31)
Image=/design/standard/images/rss_image.gif
# Category, from which the articles will be fetched (0=complete site) 
# and number of articles in Feed
CategoryID=0
Limit=10

[eZBugMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=images/standard/
Language=en_GB
DefaultSection=2
MailAccount=bug
MailPassword=tjobing
MailServer=mail.example.org
MailServerPort=110
MailReplyToAddress=bug@example.org


[eZBulkMailMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=4
UseBulkmailSenderDefaults=enabled
BulkmailSenderAddress=admin@example.org
BulkmailSenderName=Administrator
UseEZUser=disabled


[eZCalendarMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
DayStartTime=08:00
DayStopTime=18:00
DayInterval=00:30
DefaultSection=1
OnlyShowTrustees=disabled


[eZCCMain]
PID=fi345g121net32it77
Language=0
VendorID=252
i=
Currency=0
p=0
DefaultSection=1


[eZContactMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
DocumentRoot=./ezcontact/
Language=en_GB
CategoryImageWidth=100
CategoryImageHeight=100
MaxPersonConsultationList=5
MaxCompanyConsultationList=5
MaxPersonList=25
MaxCompanyList=25
CompanyOrder=name
MaxCountryList=11
MaxRegionList=11
LastConsultations=5
AddressMinimum=1
AddressWidth=1
PhoneMinimum=2
PhoneWidth=2
OnlineMinimum=2
OnlineWidth=2
CompanyViewLogin=true
CompanyEditLogin=true
ShowCompanyContact=true
ShowCompanyStatus=true
DefaultSection=2
ShowAllConsultations=disabled
ShowRelatedConsultations=enabled

[eZErrorMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=1


[eZExampleMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=1


[eZFileManagerMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=images/standard/
Language=en_GB
SearchListLimit=40
DefaultSection=2
AutoSyncronize=1
LocalSyncronizeDir=/home/jhe/sync
SyncronizeReadGroup=2
SyncronizeWriteGroup=1
SyncronizedFilesOwner=1
Limit=50
ShowUpFolder=enabled
DownloadOriginalFilename=false


[eZFormMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=1
DefaultElementName=New element
AdminFormListLimit=20
UseDefaultRedirectPage=disabled
DefaultRedirectPage=/select/url
UseDefaultInstructionPage=disabled
DefaultInstructionPage=/select/url
CreateEmailDefaults=disabled


[eZForumMain]
AdminTemplateDir=templates/standard/
Language=en_GB
TemplateDir=templates/standard/
DocumentRoot=./ezforum/
ReplyPrefix=RE: 
PageCaching=enabled
MessageUserLimit=40
SearchUserLimit=40
SearchAdminLimit=10
SimpleUserList=40
MessageAdminLimit=20
UnApprovdLimit=2
FutureDate=In the future
AllowedTags=<a>,<i>,<b>,<blockquote>,<p>,<div>
ReplyStartTag=<blockquote>
ReplyEndTag=</blockquote>
ReplyTags=disabled
AllowHTML=disabled
AnonymousPoster=Anonymous
ReplyAddress=noreply@example.org
ShowReplies=disabled
# Number of days to count messages as new
NewMessageLimit=2
DefaultSection=2


[eZImageCatalogueMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
ImageViewWidth=400
ImageViewHeight=500
ThumbnailViewWidth=150
ThumbnailViewHight=150
ShowOriginal=disabled
ListImagesPerPage=10
ListImagesPerRow=4
DefaultSection=2
# enabled = use normal header/footer; disabled = use print header/footer
SlideShowHeaderFooter=disabled
SlideShowOriginalImage=disabled


[eZLinkMain]
AdminTemplateDir=templates/standard/
DocumentRoot=./ezlink/
TemplateDir=templates/standard/
PageCaching=enabled
Language=en_GB
CategoryImageWidth=150
CategoryImageHeight=150
LinkImageWidth=150
LinkImageHeight=150
UserSearchLimit=40
UserLinkLimit=40
AdminLinkLimit=20
AdminAcceptLimit=20
AdminSearchLimit=20 
DefaultSection=1
AcceptSuggestedLinks=0
CategoryIDSequence=

[eZMailMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=2
ReplyPrefix=Re: 
HTMLMail=enabled
MailPerPageDefault=40


[eZMediaCatalogueMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
ListMediaPerPage=10
ListMediaPerRow=4
DefaultSection=1


[eZMessageMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=1


[eZModuleMain]
Language=en_GB
DefaultSection=1


[eZNewsFeedMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=images/standard/
Language=en_GB
PageCaching=enabled
DefaultSection=4


[eZPollMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
DocumentRoot=./ezpoll/
PageCaching=enabled
DefaultSection=1
AllowDoubleVotes=disabled

# if eZ poll should check IP or cookie
# valid values: ip | cookie
DoubleVoteCheck=ip


[eZQuizMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
AdminListLimit=30
ListLimit=20
PageCaching=enabled
ScoreLimit=10
ScoreCurrent=enabled
DefaultSection=1


[eZSearchMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
#Contains the modules to search through. E.g. eZArticle;eZForum
SearchModules=eZArticle;eZContact;eZForum;eZTrade
DefaultSection=1


[eZSiteManagerMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
AdminListLimit=20
DefaultSection=1


[eZStatsMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
StoreStats=enabled
DefaultSection=1


[eZSysinfoMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=1


[eZTodoMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
DocumentRoot=./eztodo/
NotDoneID=2
DoneID=1
DefaultSection=2


[eZTradeMain]
AdminTemplateDir=templates/standard/
DocumentRoot=./eztrade/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
PageCaching=enabled
OrderSenderEmail=nospam@example.org
OrderReceiverEmail=nospam@example.org
mailToAdmin=nospam@example.org
HotDealColumns=1
HotDealImageWidth=40
HotDealImageHeight=40
Checkout=standard
MainImageWidth=300
MainImageHeight=300
SmallImageWidth=150
SmallImageHeight=150
ThumbnailImageWidth=140
ThumbnailImageHeight=140
ShowBillingAddress=enabled
ForceSSL=choose
ProductLimit=10
ProductSearchLimit=10
PriceGroupsEnabled=true
RequireUserLogin=disabled
StandardOptionHeaders=
MinimumOptionHeaders=1
MinimumOptionValues=1
SimpleOptionHeaders=true
ShowQuantity=true
ShowNamedQuantity=true
RequireQuantity=true
ShowOptionQuantity=true
ShowModuleLinker=true
ModuleList=eZContact;eZArticle
DefaultSectionName=Related Links
ProductLinkSections=
DiscontinueQuantityless=false
PurchaseProduct=true
MailEncrypt=none
RecipientGPGKey=KeyHolder
ApacheUser=UserApacheRunsAs
MaxSearchForProducts=200
DefaultSection=3
CategoryImageWidth=150
CategoryImageHeight=150
ShowOrderStatusToUser=true

# If this setting is set to enabled the system will use the countrylist to decide 
# if prices and totals will include VAT.
CountryVATDiscrimination=enabled

# If this setting is set to enabled prices shown to anonymous users will include VAT.
PricesIncVATBeforeLogin=enabled

# If this setting is set to enabled prices shown to logged in users will include 
# VAT ( This will be overridden by the CountryVATDiscrimination variable ).
PricesIncludeVAT=enabled

ShowExTaxColumn=enabled
ShowIncTaxColumn=enabled

# This is the columns which will be shown in the admin interface and should be
# set to enabled since most countries require you to have this information.
AdminShowExTaxColumn=enabled
AdminShowIncTaxColumn=enabled

# This setting will be overridden by the ShowExTaxColumn setting.
ShowExTaxTotal=enabled

# Is the number of columns which are the difference between the product list and the totals below!
# Default is 9 - 3 = 6
ColSpanSizeTotals=6

# Send e-mail to seller n days before sold service expires (-1 dissables)
EmailBeforeExpire=2

# Should TAX be enabled by default if user is not logged in?
NoUserShowVAT=disabled

[Checkout]
#PaymentMethods=allowed payment methods, Name1|file1.php;Name2|file2.php
#Allowable:
#PaymentMethods=VISA/MC|visa.php;Paypal|paypal.php;Invoice|invoice.php
#DO NOT delete or alter sequence once live!  Add new methods to end.
#First method listed is default
PaymentMethods=VISA/MC|visa.php;Paypal|paypal.php;Invoice|invoice.php

#PaypalMode=Paypal (LIVE) or Sandbox (TEST)
#see www.paypal.com or www.sandbox.paypal.com for info
PaypalMode=Sandbox
#PaypalEmail=primary Paypal email/login
PaypalEmail=admin@example.org
#CurrencyCode=currency code used: USD, EUR, GBP, CAD, or JPY
CurrencyCode=USD
#SiteLogo=complete URL of the 150x50-pixel image you would like to use as your logo. 
#Only recommended for SSL-hosted images (https://), else buyer will see security popup
SiteLogo=
#PageStyle=Sets the Custom Payment Page Style for Paypal payment pages (optional)
PageStyle=
#LanguageCode=Buyer Paypal checkout language. US (US English), UK (UK English),
#DE (German), JP (Japanese)
LanguageCode=US
#Variables to support Authorize.net payment (future gateway)
#TransKey=authorize.net merchant transaction key
AuthTransKey=
#AuthLogin=authorize.net merchant login
AuthLogin=
#AuthPassword=authorize.net merchant password
AuthPassword=

[eZURLTranslatorMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
Language=en_GB
DefaultSection=1


[eZUserMain]
AdminTemplateDir=templates/standard/
TemplateDir=templates/standard/
ImageDir=/images/standard/
Language=en_GB
DocumentRoot=./ezuser/
AnonymousUserGroup=2
SelectCountry=enabled
SelectRegion=enabled
UserWithAddress=enabled
RequireUserLogin=disabled
SimultaneousLogins=enabled
DefaultSimultaneousLogins=0
MaxUserList=20
DefaultCountry=240
DefaultRegion=2
DefaultRedirect=/
DefaultSection=1
ReminderMailFromAddress=nospam@example.org
RequireAddress=disabled
RequireFirstAddress=disabled
OverrideUserWithAddress=disabled
UserPersonLink=enabled

[eZXMLRPC]
UserIndex=/index.php

*/ ?>
