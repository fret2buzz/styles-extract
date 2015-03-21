# styles-extract

>Extracts all styles which match a certain pattern. For example extract all rules that contain pixel values.

#Overview

Configuration is in the head of the script


#Getting started
PHP is required. [Installation and Configuration](http://php.net/manual/en/install.php)

Once it has been installed simply run this in your command line
``` 
php styles-only-that-have.php
```
#Expected result
For example we want to grab all rules that have pixels values in the declarations 
>Source: main.css
``` css
.test {
	font-size: 12px;
	color: red;
}
@media (max-width: 980px) {
	.test2 {
		background: blue;
		width: 500px;
	}
}
```
>Generated file: main-only.css
``` css
.test {
	font-size: 12px;
}
@media (max-width: 980px) {
	.test2 {
		width: 500px;
	}
}
```



