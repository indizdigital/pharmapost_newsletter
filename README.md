# Basic Configuration

databaseTable = tx_phinewsamedisch_domain_model_news<br/>
databaseTableCategory = tx_phinewsamedisch_domain_model_category<br/>
categoryFieldInItemTable = category<br/>
languageFile = typo3conf/ext/phi_newsletter/Resources/Private/Language/locallang.php<br/>
templatePath = fileadmin/templates/amedis/ext/phi_newsletter/v1<br/>
teaserField = shorttext<br/>
altTeaserField = bodytext<br/>
altTeaserLength = 256<br/>
uidMapping.68 = 68<br/>
getParamScope = tx_phinewsamedisch_news<br/>
redirectController = News<br/>
redirectAction = show<br/>
showSubheaderOnce = 1<br/>
categoryname.2.0 = Spezialangebot<br/>
categoryname.2.1 = Spézialangebot<br/>
categoryname.3.0 = Dienstleistungen<br/>
categoryname.3.1 = Diènstleistungen<br/>
categoryname.4.0 = Industrieinformationen<br/>
categoryname.4.1 = Industriéinformationen

## templatePath
The template name will be generate out of the templatePath. The last directory will transformed as follow<br>
_ will be replaced by SPACE
All single words will be capitalized

## altTeaserField
If the value of the teaserField of the current record (which should be rendered into the Newsletter) is empty, the altTeaserField will be cropped to the altTeaserLength and inserted

## uidMapping
To link the Newsletter Items, you can add a Page Mapping for the Links. Lefthandside this is the uid of the page where the Item is saved (the pid of the record) while the righthandside is the link target uid.

## categoryname
Translation of the category name. Second Param is the Category Uid, the Third Param is the Language Uid.

## showSubheaderOnce
If set to one, the category subheader Template (subheader.html) is only include once.

## categoryFieldInItemTable
Field where the category 'databaseTableCategory' is set

## databaseTableCategory
if this is set to a table, the items will be sorted and displayed by the first catgory assigned to the item
