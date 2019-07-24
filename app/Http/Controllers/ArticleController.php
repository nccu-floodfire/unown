<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Encoder;
use App\Article;
use App\Result;

class ArticleController extends Controller
{
    /**
     * 取得該編碼員所需文章
     *
     * @param Request $request
     * @param integer $encoderUuid
     * @param integer $articleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne(Request $request, $encoderUuid, $articleId)
    {
        $encoder = Encoder::where('access_token', $encoderUuid)->first();
        if (is_null($encoder)) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => "User Not Found"
            ], 422);
        }
        $article_list = $encoder->getArticleList();
        $articleId = intval($articleId);
        if (!in_array($articleId, $article_list)) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => "Wrong User Article Pair"
            ], 422);
        }
        $article = Article::where('id', $articleId)->first();
        if (is_null($article)) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => "Article Not Found"
            ], 422);
        }
        $result = $encoder->results->where('article_id', $articleId);
        $output = ['article' => $article,
                   'result' => $result];
                   
        return (response()->json(
            $output,
            200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], 
            JSON_UNESCAPED_UNICODE
        ));
    }

    /**
     * 回答某一題
     *
     * @param Request $request
     * @param integer $encoderUuid
     * @param integer $articleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function answerOne(Request $request, $encoderUuid, $articleId)
    {
        $validator = Validator::make($request->all(), [
            'result_id' => 'nullable|numeric',
            'quote_content' => 'nullable|string',
            'quote_origin' => 'nullable|string',
            'quote_actual' => 'nullable|string',
            'quote_pos' => [
                'nullable',
                Rule::in(['0','1','2'])
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => $validator->messages()
            ], 422);
        }
        
        $encoder = Encoder::where('access_token', $encoderUuid)->first();
        if (is_null($encoder)) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => "User Not Found"
            ], 422);
        }
        $article_list = $encoder->getArticleList();
        $articleId = intval($articleId);
        if (!in_array($articleId, $article_list)) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => "Wrong User Article Pair"
            ], 422);
        }
        $article = Article::where('id', $articleId)->first();
        if (is_null($article)) {
            return response()->json([
                'status' => 422,
                'type' => 'error',
                'message' => "Article Not Found"
            ], 422);
        }

        if (is_null($request->result_id)) {
            $result = new Result;
            $result->encoder_id = $encoder->id;
            $result->article_id = $articleId;
            $result->quote_content = null;
            $result->quote_origin = null;
            $result->quote_actual = null;
            $result->quote_pos = null;
        } else {
            $result = $encoder->results->where('article_id', $articleId)->
                      where('id', $request->result_id)->first();
        }
        $result->quote_content = $request->has('quote_content') ? $request->quote_content : $result->quote_content;
        $result->quote_origin = $request->has('quote_origin') ? $request->quote_origin : $result->quote_origin;
        $result->quote_actual = $request->has('quote_actual') ? $request->quote_actual : $result->quote_actual;
        $result->quote_pos = $request->has('quote_pos') ? $request->quote_pos : $result->quote_pos;
        $result->save();

        $response['status'] = 201;
        $response['type'] = 'success';
        $response['message'] = 'Article ' . $articleId . ' has been asnwered.';

        return($response);
    }
}
