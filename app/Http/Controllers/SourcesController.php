<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;

class SourcesController extends Controller
{
    /**
     * Set the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $page
     * @return \Illuminate\Http\Response
     */
    public function SetFilteredColumns(Request $request, $page)
    {
        // get filtered columns and convert to string
        $filtered_columns = implode(',', $request->columns);

        // add filtered columns to the page
        Source::updateOrCreate(
            ['page_id' => $page],
            ['columns' => $filtered_columns]
        );

        return response()->json([
            'status' => 'success',
        ]);
    }

}
