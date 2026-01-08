# Bible In One Year Wordpress Plugin
BIOY is a simple Romanian / English Wordpress Plugin that displays a daily reading plan for the current month.  

# Usage

`[bioy lang='en' translation='esv' col='3' locale='en_US' display_current_month='yes']`

### Optional Parameters

- `translation` is used to specify which translation you would like to be dispyed.  See https://www.biblegateway.com/versions/ for avaialble translation
- `col` is used to specify how many columns are dispalyed. If not specified, the default is 3 columns. 
- `locale` is used to display the date in the proper language. If not specified, the default is based on your hosting provider. 
- `display_current_month` is used to toggle the current Month/Year.  If not specified, the default is No

### Adding a New lanauge
This plugin can easilbe be extended to other lanagues by simply creating a new bioy_**langage**.xml file.  Simply copy an exsiting file, rename it to the language code that the file has been translated to, translate books (https://www.biblegateway.com/versions/New-King-James-Version-NKJV-Bible/#booklist) and reference the lanaguage in the shortcode.