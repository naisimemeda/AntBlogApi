<?php

namespace App\Console\Commands\Elasticsearch;

use App\Models\Article;
use Illuminate\Console\Command;

class SyncArticles extends Command
{
    protected $signature = 'es:sync-articles';

    protected $description = '将文章数据同步到 Elasticsearch';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $es = app('es');

        Article::query()->with(['tags'])->chunkById(100, function ($articles) use ($es) {
            $req = ['body' => []];
            $this->output->progressStart(count($articles));
            foreach ($articles as $article) {
                $data = $article->toESArray();

                $req['body'][] = [
                    'index' => [
                        '_index' => 'articles',
                        '_type'  => '_doc',
                        '_id'    => $data['id'],
                    ],
                ];
                $req['body'][] = $data;
                $this->output->progressAdvance();
            }
            $this->output->progressAdvance();
            try {
                // 使用 bulk 方法批量创建
                $es->bulk($req);
                $this->output->progressFinish();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        });
        $this->info('同步完成');
    }

}
