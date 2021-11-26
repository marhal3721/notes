# cs
* [php_codesniffer](https://packagist.org/packages/squizlabs/php_codesniffer)

```bash
vendor/bin/phpcbf [dir1 dir2 file1 file2]
vendor/bin/phpcs --report-file=cs-report.txt
```
# md
* [phpmd](https://packagist.org/packages/phpmd/phpmd)

```bash
php -d memory_limit=-1 vendor/bin/phpcpd [dirs] tests --log-pmd=cpd-log.xml
```

# cpd
* [phpcpd](https://packagist.org/packages/sebastian/phpcpd)

```bash
vendor/bin/phpmd [dirs] text ruleset.xml --reportfile md-src-report.txt
```
# unit
* [phpunit](https://packagist.org/packages/phpunit/phpunit)

```bash
vendor/bin/phpunit --stop-on-failure --log-junit junit.xml --coverage-text=coverage.txt --coverage-html=UnitTestCover
```

# phploc 测量PHP项目大小和分析结构
* [phploc](https://packagist.org/packages/phploc/phploc)

# pdepend 软件分析器和度量工具
* [pdepend](https://packagist.org/packages/pdepend/pdepend)
* [Doc](https://pdepend.org/documentation/getting-started.html)
* [Doc](https://www.testwo.com/blog/7640)

# faker mock数据生成器
* [faker](https://packagist.org/packages/fakerphp/faker)
