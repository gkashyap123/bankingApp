<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable'
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer added');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer updated');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted');
    }

    /**
     * Delete all customers (admin only)
     */
    public function destroyAll(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        \DB::beginTransaction();
        try {
            // Delete related events first to avoid FK constraint issues
            \App\Models\Event::query()->delete();

            // Delete customers
            \App\Models\Customer::query()->delete();

            \DB::commit();
            return redirect()->route('customers.index')->with('success', 'All customers deleted');
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Failed deleting all customers: '.$e->getMessage());
            return back()->with('error', 'Failed to delete all customers');
        }
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('customers.import');
    }


    /**
     * Import customers from uploaded CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('file')->getRealPath();

        if (!$path || !file_exists($path)) {
            return back()->with('error', 'Uploaded file not found');
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Unable to open uploaded file');
        }

        $header = null;
        $created = 0;
        $updated = 0;

        \DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (!$header) {
                    // normalize header keys to lowercase without BOM
                    $header = array_map(function ($h) {
                        $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
                        return strtolower(trim($h));
                    }, $row);

                    if (!in_array('name', $header)) {
                        throw new \Exception('CSV must contain a "name" column');
                    }

                    continue;
                }

                // pad row if shorter than header
                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), null);
                }

                $data = array_combine($header, $row);
                // trim values
                $data = array_map(function ($v) {
                    return $v === null ? null : trim($v);
                }, $data);

                $name = $data['name'] ?? null;
                if (!$name) {
                    // skip rows without a name
                    continue;
                }

                $email = $data['email'] ?? null;
                $phone = $data['phone'] ?? null;

                // parse dates safely
                $dob = null;
                if (!empty($data['dob'])) {
                    try { $dob = \Carbon\Carbon::parse($data['dob'])->toDateString(); } catch (\Exception $e) { $dob = null; }
                }

                $anniversary = null;
                if (!empty($data['anniversary'])) {
                    try { $anniversary = \Carbon\Carbon::parse($data['anniversary'])->toDateString(); } catch (\Exception $e) { $anniversary = null; }
                }

                $notes = $data['notes'] ?? null;

                // handle investments: accept JSON or delimited list
                $investments = null;
                if (!empty($data['investments'])) {
                    $inv = $data['investments'];
                    $decoded = json_decode($inv, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $investments = $decoded;
                    } else {
                        $parts = preg_split('/[;,|]/', $inv);
                        $parts = array_filter(array_map('trim', $parts), function ($x) { return $x !== ''; });
                        $investments = $parts ? array_values($parts) : null;
                    }
                }

                $payload = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'dob' => $dob,
                    'anniversary' => $anniversary,
                    'notes' => $notes,
                    'investments' => $investments,
                ];

                // find existing by email first, then phone
                $customer = null;
                if ($email) {
                    $customer = Customer::where('email', $email)->first();
                }
                if (!$customer && $phone) {
                    $customer = Customer::where('phone', $phone)->first();
                }

                if ($customer) {
                    $customer->update($payload);
                    $updated++;
                } else {
                    Customer::create($payload);
                    $created++;
                }
            }

            fclose($handle);
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            if (is_resource($handle)) fclose($handle);
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }

        return redirect()->route('customers.index')->with('success', "Import completed. Created: $created, Updated: $updated");
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate()
    {
        $columns = ['name','email','phone','dob','anniversary','notes','investments'];
        $content = implode(',', $columns)."\n";
        $content .= "John Doe,john@example.com,+1234567890,1980-01-01,2005-05-20,Example notes,'[]'\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers_template.csv"'
        ]);
    }
}
