# styles-only-that-have

>Grab all styles which match a certain pattern. For example grab all styles that have pixel values.

#Overview

Configuration is in the head of the script


#Getting started
PHP is required. [Installation and Configuration](http://php.net/manual/en/install.php)

Once it has been installed simply run this in your command line
``` 
php styles-only-that-have.php
```
#Expected result
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



