<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Encoder;
use App\Article;

class EncoderController extends Controller
{
    /**
     * 回傳所有需答題的id以及是否有回答過
     *
     * @param Request $request
     * @param integer $encoderUuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function answerSheet(Request $request, $encoderUuid)
    {
        $encoder = Encoder::where('access_token', $encoderUuid)->first();
        if (is_null($encoder)) {
            abort(403, 'Encoder Not Exist.');
        }
        $article_list = Article::whereIn('id', $encoder->getArticleList())->orderBy('id')->get();
        if (sizeof($article_list) == 0) {
            abort(403, 'Objects Have not been Settled.');
        }

        $question_list = $article_list->map(function ($article) use ($encoder) {
            return( ['article_id' => $article->id,
                    'is_answered' => $article->results->where('encoder_id', $encoder->id)->count()>0,
                    'answer_time' => $article->results->where('encoder_id', $encoder->id)->count()] );
        });
        return(view('answerSheet', compact('encoderUuid', 'question_list')));
    }
}
