<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Encoder;
use App\Result;

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
        $article_list = $encoder->getArticleList();
        if (sizeof($article_list) == 0) {
            abort(403, 'Objects Have not been Settled.');
        }
        $result_list = Result::where('encoder_id', $encoder->id)->pluck('id')->toArray();
        $question_list = array_map(function ($article_id) use ($result_list) {
            return( ['article_id' => $article_id,
                    'is_answered' => in_array($article_id, $result_list)] );
        }, $article_list);
        return(view('answerSheet', compact('encoderUuid', 'question_list')));
    }
}
