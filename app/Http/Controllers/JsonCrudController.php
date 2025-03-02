<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class JsonCrudController extends Controller
{
  private $jsonFile = 'storage/app/data.json';

  // Fungsi untuk membaca data dari file JSON
  private function readJson()
  {
    if (!File::exists($this->jsonFile)) {
      return [];
    }

    $json = File::get($this->jsonFile);
    return json_decode($json, true) ?? [];
  }

  // Fungsi untuk menyimpan data ke file JSON
  private function writeJson($data)
  {
    File::put($this->jsonFile, json_encode($data, JSON_PRETTY_PRINT));
  }

  // Menampilkan semua data
  public function index()
  {
    return response()->json($this->readJson());
  }

  // Menyimpan data baru
  public function store(Request $request)
  {
    $data = $this->readJson();
    $newData = [
      'id' => count($data) + 1,
      'name' => $request->input('name'),
      'email' => $request->input('email'),
    ];

    $data[] = $newData;
    $this->writeJson($data);

    return response()->json(['message' => 'Data saved', 'data' => $newData]);
  }

  // Menampilkan data berdasarkan ID
  public function show($id)
  {
    $data = $this->readJson();
    $item = collect($data)->firstWhere('id', $id);

    return $item ? response()->json($item) : response()->json(['message' => 'Not found'], 404);
  }

  // Mengupdate data
  public function update(Request $request, $id)
  {
    $data = $this->readJson();
    $index = collect($data)->search(fn($item) => $item['id'] == $id);

    if ($index === false) {
      return response()->json(['message' => 'Not found'], 404);
    }

    $data[$index]['name'] = $request->input('name', $data[$index]['name']);
    $data[$index]['email'] = $request->input('email', $data[$index]['email']);

    $this->writeJson($data);

    return response()->json(['message' => 'Data updated', 'data' => $data[$index]]);
  }

  // Menghapus data berdasarkan ID
  public function destroy($id)
  {
    $data = $this->readJson();
    $filteredData = array_values(array_filter($data, fn($item) => $item['id'] != $id));

    if (count($data) === count($filteredData)) {
      return response()->json(['message' => 'Not found'], 404);
    }

    $this->writeJson($filteredData);

    return response()->json(['message' => 'Data deleted']);
  }
}