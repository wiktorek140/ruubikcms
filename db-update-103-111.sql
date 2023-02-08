/* RuubikCMS database schema update 1.0.3 -> 1.1.1

   It is necessary to update your version 1.0.3 (and prior) SQLite database file "/ruubikcms/sqlite/ruubikcms.sqlite"
   
   You can use command line like: 
   
   sqlite3 test.db < db-update-103-110.sql
   
   or copy & paste statements below to a handy Mozilla Firefox add-on called SQLite Manager and "Execute SQL".
   
*/

CREATE TABLE "extrapage" ("pageurl" text PRIMARY KEY ,"name" text,"title" text,"header1" text,"description" text,"keywords" text,"content" text,"mother" text,"levelnum" integer,"ordernum" integer,"image1" text,"image2" text,"lang" text,"pagetype" integer,"extracode" text,"status" integer, "updater" TEXT, "updated" TEXT, "creator" TEXT, "image3" TEXT);
CREATE TABLE "extrauser" ("username" text PRIMARY KEY  NOT NULL ,"password" TEXT,"firstname" TEXT,"lastname" TEXT,"email" TEXT,"phone" TEXT,"active" INTEGER,"expirytime" TEXT,"group" TEXT,"custpage" TEXT,"organization" TEXT);
CREATE TABLE "extra_dl_log" ("filename" TEXT, "username" TEXT, "ip" TEXT, "time" TEXT);
CREATE TABLE "extra_dl_count" ("filename" TEXT PRIMARY KEY  NOT NULL , "downloads" INTEGER, "count_started" TEXT, "last_dl" TEXT);
CREATE TABLE "dl_log" ("filename" TEXT,"ip" TEXT,"time" TEXT);
CREATE TABLE "dl_count" ("filename" TEXT PRIMARY KEY  NOT NULL , "downloads" INTEGER, "count_started" TEXT, "last_dl" TEXT);
ALTER TABLE "page" ADD COLUMN "image3" TEXT;
ALTER TABLE "site" ADD COLUMN "no_image3" INTEGER;
ALTER TABLE "options" ADD COLUMN "use_help" INTEGER;
ALTER TABLE "options" ADD COLUMN "pagination_rows" INTEGER;
ALTER TABLE "options" ADD COLUMN "tinymce_extent" INTEGER;
