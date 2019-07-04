<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProposalRequest;
use App\Models\Category;
use App\Proposal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProposalController extends Controller
{
    public function viewAll(Request $request) {
        $query = Proposal::with('user');

        if ($request->get('category', -1) !== -1) {
            $query->when('category_id', '=', $request->get('category'));
        }

        if ($request->get('date', '') !== '') {
            $query->where('date', '=', $request->get('date'));
        }

        if ($request->get('location', '') !== '') {
            $query->whereHas('place', '=', $request->get('location'));
        }

        return response()->json([
            'status' => true,
            'data' => $query->orderBy('id', 'desc')->get()
        ]);
    }

    public function view(Proposal $proposal) {
        return [
            'status' => true,
            'data' => Proposal::with('user', 'category')->find($proposal->id)
        ];
    }

    public function store(StoreProposalRequest $request) {
        /**
         * @var Category $category
         */
        $category = Category::find($request->get('category_id'));

        $proposal = new Proposal($request->all());
        $proposal->category()->associate($category);
        $proposal->user()->associate(Auth::user());
        $proposal->save();

        return response()->json([
            'status' => true,
            'data' => $proposal
        ]);
    }

    public function delete(Proposal $proposal) {
        if (Gate::allows('delete-proposal', $proposal)) {
            try {
                $proposal->delete();
                return response(null, 204);
            } catch (\Exception $ex) {
                report($ex);
                abort(500, $ex->getMessage());
            }
        } else {
            abort(403);
        }
    }

}
