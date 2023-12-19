<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['permission:edit article','auth:sanctum']); // if U want to add middleware to Controller
    //     // return redirect(route('logout'));
    // }

    public function index()
    {
        $articles = Article::paginate(20);
        if($articles){
            return response()->json([
                'data' => $articles->items(),
                'message'=>'Request was successful',
                'statusCode' => Response::HTTP_OK,
            ]);
        }
        return response()->json([
            'data' => null,
            'message'=>'Data not found',
            'statusCode' => Response::HTTP_NO_CONTENT,
        ]);
    }
    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if(!$user->can('create article')){
            return response()->json([
                'message'=>'U are not authorize to create articles & U will be redirected to another page after 2 seconds',
                'statusCode'=>Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
            ],Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        $request->validate([
            'name'=>['required','string','min:5',],
            'description'=>['required','string','min:10'],
        ]);
        $createArticle = Article::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'creator_id'=>$user->id,
        ]);
        return response()->json([
            'data'=>$createArticle,
            'message'=>'Request wwas successful',
            'status'=>true,
            'statusCode'=>Response::HTTP_CREATED,
        ]);
    }
    public function show($id)
    {
        $article = Article::find($id);
        if($article){
            return response()->json([
                'data' => $article,
                'message'=>'Request was successful',
                'statusCode' => Response::HTTP_OK,
            ]);
        }
        return response()->json([
            'data' => null,
            'message'=>'Data not found',
            'statusCode' => Response::HTTP_NO_CONTENT,
        ]);
    }
    public function update(Request $request,$id)
    {
        $user = User::find(Auth::user()->id);
        if(!$user->can('edit article')){
            return response()->json([
                'message'=>'U are not authorize to edit articles & U will be redirected to another page after 2 seconds',
                'statusCode'=>Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
            ],Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        $article = Article::find($id);
        if($article){

            $request->validate([
                'name'=>['required','string','min:5'],
                'description'=>['required','string','min:10'],
            ]);
            $article->update([
                'name'=>$request->name,
                'description'=>$request->description,
            ]);
            return response()->json([
                'data' => $article,
                'message'=>'Data Has updated',
                'statusCode' => Response::HTTP_OK,
            ]);
        }

        return response()->json([
            'data' => null,
            'message'=>'Data not found',
            'statusCode' => Response::HTTP_NO_CONTENT,
        ]);
    }
    
    public function destroy($id)
    {
        $user = User::find(Auth::user()->id);
        if(!$user->can('delete article')){
            return response()->json([
                'message'=>'U are not authorize to delete articles & U will be redirected to another page after 2 seconds',
                'statusCode'=>Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
            ],Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        $article = Article::find($id);
        if($article){
            $article->delete();
            return response()->json([
                'message'=>'Data Has deleted',
                'statusCode' => Response::HTTP_OK,
            ]);
        }
    
        return response()->json([
            'data' => null,
            'message'=>'Data not found',
            'statusCode' => Response::HTTP_NO_CONTENT,
        ]);
        //
    }
}
