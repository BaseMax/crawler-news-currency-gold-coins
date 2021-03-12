# crawler-news-currency-gold-coins

PHP

### Database

```sql
--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint(50) NOT NULL,
  `source_id` bigint(20) NOT NULL,
  `title` text NOT NULL,
  `subtitle` text DEFAULT NULL,
  `slug` text NOT NULL,
  `text` longtext DEFAULT NULL,
  `subtext` text DEFAULT NULL,
  `source` int(11) NOT NULL DEFAULT 1,
  `link` text NOT NULL,
  `image` text DEFAULT NULL,
  `video` text DEFAULT NULL,
  `date` varchar(40) NOT NULL,
  `time` varchar(20) NOT NULL,
  `view` int(11) NOT NULL DEFAULT 0,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Crontab

#### $ crontab -l

```
0 */3 * * * php /path/to/dir/parse.php
```
