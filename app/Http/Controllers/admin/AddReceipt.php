<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AddReceipt extends Controller
{
  public function index()
  {
    return view('content.pages.pages-addreceipt');
  }

  // public function getProducts()
  // {
  //   $products = DB::table('price_list')->select('deskripsi')->distinct()->get();
  //   return response()->json($products);
  // }

  // public function getTipe()
  // {
  //   $products = [
  //     ['code' => 'hnd', 'name' => 'Honda'],
  //     ['code' => 'ymh', 'name' => 'Yamaha'],
  //     ['code' => 'szk', 'name' => 'Suzuki'],
  //     ['code' => 'kws', 'name' => 'Kawasaki'],
  //     ['code' => 'oth', 'name' => 'Other'],
  //   ];

  //   return response()->json($products);
  // }

  public function getTipe()
  {
    try {
      // Cek koneksi database sebelum query
      DB::connection()->getPdo();

      // Data manual karena ini bukan query ke DB
      $products = [
        ['code' => 'hnd', 'name' => 'Honda'],
        ['code' => 'ymh', 'name' => 'Yamaha'],
        ['code' => 'szk', 'name' => 'Suzuki'],
        ['code' => 'kws', 'name' => 'Kawasaki'],
        ['code' => 'oth', 'name' => 'Other'],
      ];
      return response()->json($products, 200);
    } catch (\Exception $e) {
      Log::error('Database error: ' . $e->getMessage());

      return response()->json(['error' => 'Database connection lost'], 500);
    }
  }

  public function getProducts()
  {
    try {
      DB::connection()->getPdo();

      $products = DB::table('price_list')
        ->select('deskripsi')
        ->distinct()
        ->get();

      if ($products->isEmpty()) {
        return response()->json(['message' => 'No data available'], 200);
      }

      return response()->json($products, 200);
    } catch (\Exception $e) {
      Log::error('Supabase DB error: ' . $e->getMessage());

      return response()->json(['error' => 'Database connection failed'], 500);
    }
  }


  public function ccMotor(Request $request)
  {
    $desc = $request->input('desc');
    $type = $request->input('type');

    $products = [];
    $price = null;

    if ($desc) {
      $products = DB::table('price_list')
        ->where('deskripsi', $desc)
        ->get();
    }

    if ($type) {
      $price = DB::table('price_list')
        ->where('product', $type)
        ->value('price'); // Ambil langsung harga tanpa array
    }

    return response()->json([
      'products' => $products,
      'price' => $price, // Akan bernilai null jika tidak ditemukan
    ]);
  }

  public function addReceiptTbl(Request $request)
  {
    $request->validate([
      'region' => 'required|string|max:2',
      'number' => 'required|string|max:4',
      'series' => 'required|string|max:3',
      'tipe_motor' => 'required|string',
      'product_id' => 'required|string',
      'cc_motor' => 'required|string',
      'checkPrice' => 'required|numeric',
      'payment' => 'required|string'
    ]);

    $merekMotor = '';

    if ($request->tipe_motor === 'hnd') {
      $merekMotor = 'Honda';
    } elseif ($request->tipe_motor === 'ymh') {
      $merekMotor = 'Yamaha';
    } elseif ($request->tipe_motor === 'szk') {
      $merekMotor = 'Suzuki';
    } elseif ($request->tipe_motor === 'kws') {
      $merekMotor = 'Kawasaki';
    } else {
      $merekMotor = 'Other';
    }

    $find = $request->cc_motor;
    $getData = DB::table('price_list')
      ->where('product', $find)
      ->first();

    $id_descProduct = $getData->product;
    $ccMotor = $getData->cc_motor;

    $rndm = strtoupper(Str::random(5));
    $nopol = $request->region . $request->number . $request->series;

    $receipt = DB::table('receipt_tbl')->insert([
      'uuid' => $rndm . $nopol,
      'nopol' => $nopol,
      'merek_motor' => $merekMotor,
      'price_list_deskripsi' => $request->product_id,
      'price_list_product' => $id_descProduct,
      'cc_motor' => $ccMotor,
      'payment' => $request->payment,
      'price' => $request->checkPrice,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    // Beri response JSON
    return response()->json([
      'success' => true,
      'message' => 'Data berhasil ditambahkan!',
      'data' => $receipt
    ]);
  }

  // public function getAllReceipt()
  // {
  //   $alldataReceipt = DB::table('receipt_tbl')->orderBy('created_at', 'desc')->get();
  //   $draw = count($alldataReceipt) > 0 ? 1 : 0;
  //   $totalRecords = DB::table('receipt_tbl')->count();

  //   return response()->json([
  //     'draw' => $draw,
  //     'recordsTotal' => $totalRecords,
  //     'recordsFiltered' => $totalRecords,
  //     'data' => $alldataReceipt->isNotEmpty() ? $alldataReceipt : []
  //   ]);
  // }

  public function getAllReceipt()
  {
    try {
      $alldataReceipt = DB::table('receipt_tbl')->orderBy('created_at', 'desc')->get();
      $totalRecords = $alldataReceipt->count();

      return response()->json([
        'draw' => request()->input('draw', 1),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $alldataReceipt->isNotEmpty() ? $alldataReceipt : []
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'draw' => request()->input('draw', 1),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Terjadi kesalahan saat mengambil data.'
      ], 200);
    }
  }
}