<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Models\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PdfTest extends TestCase
{

    public function testIndex()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function testGetPdfs()
    {
        $pdfController = new HomeController();
        $request = new Request();

        $result = $pdfController->getPDFs($request);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(0, $result->total());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(20, $result->perPage());
    }

    public function testShowPdf()
    {
        $pdf = new Pdf();
        $pdf->id = 1;
        $pdf->title = 'Sample PDF';
        $pdf->file = file_get_contents(env('PDF_FILE_PATH'));
        $pdf->save();

        $pdfController = new HomeController();

        $response = $pdfController->showPDF($pdf->id);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertEquals('inline; filename="' . $pdf->title . '"', $response->headers->get('Content-Disposition'));

        $this->assertEquals($pdf->file, $response->getContent());

        $pdf->delete();
    }

    public function testUploadPdf()
    {
        $pdfController = new HomeController();
        $title = "Test PDF";

        $pdf_data = "Test PDF contents";
        $pdf_file = tmpfile();
        fwrite($pdf_file, $pdf_data);
        $file_path = stream_get_meta_data($pdf_file)['uri'];
        $_FILES['pdfFile'] = [
            'name' => 'test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $file_path,
            'error' => UPLOAD_ERR_OK,
            'size' => strlen($pdf_data)
        ];

        $pdfController->uploadPDF($title);

        $pdf = Pdf::where('title', $title)->first();
        $this->assertNotNull($pdf);
        $this->assertEquals($title, $pdf->title);

        fclose($pdf_file);
        $pdf->delete();
    }

    public function testDeletePdf()
    {
        $pdf = new Pdf;
        $pdf->title = 'Test PDF';
        $pdf->file = 'test file contents';
        $pdf->save();

        $pdfController = new HomeController();
        $pdfController->deletePDF($pdf->id);

        $this->assertNull(Pdf::find($pdf->id));
    }
}
