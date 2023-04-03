<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Intervention\Image\ImageManager;

class HomeController
{
    public function index(): View
    {
        return view('home');
    }

    public function getPDFs(Request $request): LengthAwarePaginator
    {
        $pdfs = Pdf::paginate(20, ['*'], 'page', $request->input('page', 1));
        $pdf_info = [];

        foreach ($pdfs as $pdf) {
            // Write the PDF contents to a temporary file
            $temp_file = tempnam(sys_get_temp_dir(), 'pdf');
            file_put_contents($temp_file, $pdf->file);

            // Get the path to the first page of the PDF as a PNG image
            $image_path = tempnam(sys_get_temp_dir(), 'pdf');
            exec("pdfinfo $temp_file | grep Pages | awk '{print $2}'", $output);
            $page_count = $output[0];
            exec("pdftoppm -png -f 1 -l 1 -scale-to 300 $temp_file $image_path");
            $image_path .= "-1.png";

            // Delete the temporary files
            unlink($temp_file);

            // Create an image instance from the page image
            $manager = new ImageManager();
            $image = $manager->make($image_path)->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Delete the temporary image file
            unlink($image_path);

            $pdf_info[] = [
                'id' => $pdf->id,
                'title' => $pdf->title,
                'thumbnail' => $image->encode('data-url')->__toString(),
            ];
        }

        return new LengthAwarePaginator($pdf_info, Pdf::count(), 20);
    }

    public function showPDF($id): Response
    {
        $pdf = Pdf::findOrFail($id);

        return new Response($pdf->file, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdf->title . '"'
        ]);
    }

    public function uploadPDF(string $title): void
    {
        $pdf_contents = file_get_contents($_FILES['pdfFile']['tmp_name']);

        $pdf = new Pdf;
        $pdf->title = $title;
        $pdf->file = $pdf_contents;
        $pdf->save();
    }

    public function deletePDF($id): void
    {
        $pdf = Pdf::findOrFail($id);
        $pdf->delete();
    }
}
