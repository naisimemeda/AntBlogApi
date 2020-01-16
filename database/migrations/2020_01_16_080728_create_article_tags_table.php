<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 60)->index();
            $table->unsignedBigInteger('article_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_tags');
    }
}

curl -H'Content-Type: application/json' -XPUT http://localhost:9200/articles/_mapping/_doc?pretty -d'{
	"properties": {
    "title": {
        "type": "text",
			"analyzer": "ik_smart"
		},
		"body": {
        "type": "text",
			"analyzer": "ik_smart"
		},
		"category_id": {
        "type": "integer"
		},
		"reply_count": {
        "type": "integer"
		},
		"view_count": {
        "type": "integer"
		},
		"hot": {
        "type": "integer"
		},
		"tags": {
        "type": "nested",
			"properties": {
            "name": {
                "type": "keyword",
					"copy_to": "tags_value"
				}
			}
		}
	}
}'
