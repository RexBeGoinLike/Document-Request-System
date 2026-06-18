<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Http\Requests\DocumentReqRequest;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requestDoc = DocumentRequest::all();
        return view('requestDoc.index', compact('requestDoc'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requestDoc.create_request');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentReqRequest $request)
    {
        $validatedData = $request->validated();

        DocumentRequest::create($validatedData);

        return redirect()->route('requestDoc.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentRequest $documentRequest)
    {
        // The record is already fetched; just pass it to your blade template
        return view('requestDoc.track_request', compact('documentRequest'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentRequest $documentRequest)
    {
        return view('requestDoc.edit_request', compact('documentRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $validatedData = $request->validated();

        DocumentRequest::create($validatedData);

        return redirect()->route('requestDoc.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentRequest $documentRequest)
    {
        DocumentRequest::destroy($documentRequest->id);
        return redirect()->route('requestDoc.index');
    }
}
