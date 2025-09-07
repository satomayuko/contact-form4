<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function admin(Request $request)
    {
        $q = Contact::query()->with('category');

        if ($kw = trim((string) $request->input('keyword', ''))) {
            $kwHalf     = preg_replace('/\s+/u', ' ', mb_convert_kana($kw, 's'));
            $kwNoSpace  = str_replace(' ', '', $kwHalf);
            $like       = '%' . $kw . '%';
            $likeHalf   = '%' . $kwHalf . '%';
            $likeNoSpace = '%' . $kwNoSpace . '%';

            $q->where(function ($qq) use ($like, $likeHalf, $likeNoSpace) {
                $qq->where('last_name', 'LIKE', $like)
                    ->orWhere('first_name', 'LIKE', $like)
                    ->orWhereRaw("REPLACE(REPLACE(CONCAT(last_name, first_name), ' ', ''), '　', '') LIKE ?", [$likeNoSpace])
                    ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", [$likeHalf])
                    ->orWhere('email', 'LIKE', $like);
            });
        }

        $map = ['male' => 1, 'female' => 2, 'other' => 3];
        if ($g = $request->input('gender')) {
            if (isset($map[$g])) {
                $q->where('gender', $map[$g]);
            }
        }

        $cid = $request->input('type');
        if ($cid !== null && $cid !== '') {
            $q->where('category_id', (int) $cid);
        }

        if ($d = $request->input('date')) {
            $q->whereDate('created_at', $d);
        }

        $contacts   = $q->orderByDesc('created_at')->paginate(7)->appends($request->query());
        $categories = Category::orderBy('id')->get();

        return view('admin', compact('contacts', 'categories'));
    }

    public function index()
    {
        $categories = Category::orderBy('id')->get();
        return view('index', compact('categories'));
    }

    public function confirm(ContactRequest $request)
    {
        $action = $request->input('action');

        if ($action === 'back') {
            return redirect('/')->withInput($request->except('action'));
        }

        if ($action === 'send') {
            $data = $this->buildStoreData($request);
            Contact::create($data);
            return redirect('/thanks');
        }

        $inputs = $request->only([
            'last_name', 'first_name', 'gender', 'email',
            'tel1', 'tel2', 'tel3',
            'address', 'building', 'type', 'content',
        ]);

        $category = Category::find($inputs['type'] ?? null);
        $inputs['type_label'] = $category?->content ?? '';

        return view('confirm', compact('inputs'));
    }

    private function buildStoreData(Request $request): array
    {
        $map    = ['男性' => 1, '女性' => 2, 'その他' => 3];
        $gender = $map[$request->input('gender')] ?? 0;
        $tel    = ($request->input('tel1') ?? '')
                . ($request->input('tel2') ?? '')
                . ($request->input('tel3') ?? '');

        return [
            'last_name'   => $request->input('last_name'),
            'first_name'  => $request->input('first_name'),
            'gender'      => $gender,
            'email'       => $request->input('email'),
            'tel'         => $tel,
            'address'     => $request->input('address'),
            'building'    => $request->input('building'),
            'category_id' => $request->input('type'),
            'detail'      => $request->input('content'),
        ];
    }

    public function thanks()
    {
        return view('thanks');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin')->with('status', '削除しました');
    }

    public function export(Request $request): StreamedResponse
    {
        $q = Contact::query()->with('category');

        if ($kw = trim((string) $request->input('keyword', ''))) {
            $kwHalf     = preg_replace('/\s+/u', ' ', mb_convert_kana($kw, 's'));
            $kwNoSpace  = str_replace(' ', '', $kwHalf);
            $like       = '%' . $kw . '%';
            $likeHalf   = '%' . $kwHalf . '%';
            $likeNoSpace = '%' . $kwNoSpace . '%';

            $q->where(function ($qq) use ($like, $likeHalf, $likeNoSpace) {
                $qq->where('last_name', 'LIKE', $like)
                    ->orWhere('first_name', 'LIKE', $like)
                    ->orWhereRaw("REPLACE(REPLACE(CONCAT(last_name, first_name), ' ', ''), '　', '') LIKE ?", [$likeNoSpace])
                    ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", [$likeHalf])
                    ->orWhere('email', 'LIKE', $like);
            });
        }

        $map = ['male' => 1, 'female' => 2, 'other' => 3];
        if ($g = $request->input('gender')) {
            if (isset($map[$g])) {
                $q->where('gender', $map[$g]);
            }
        }

        $cid = $request->input('type');
        if ($cid !== null && $cid !== '') {
            $q->where('category_id', (int) $cid);
        }

        if ($d = $request->input('date')) {
            $q->whereDate('created_at', $d);
        }

        $contacts = $q->orderByDesc('created_at')->get();

        $response = new StreamedResponse(function () use ($contacts) {
            $sanitize = function ($s) {
                $s = (string) $s;
                return preg_replace("/\R|\\x{2028}|\\x{2029}/u", ' ', $s);
            };

            $writeCsvLine = function ($fp, array $row) {
                $tmp = fopen('php://temp', 'r+');
                fputcsv($tmp, $row);
                rewind($tmp);
                $line = stream_get_contents($tmp);
                $line = rtrim($line, "\n");
                fwrite($fp, $line . "\r\n");
                fclose($tmp);
            };

            $fp = fopen('php://output', 'w');
            fwrite($fp, "\xEF\xBB\xBF");

            $writeCsvLine($fp, ['お名前', '性別', 'メール', '種類', '電話番号', '住所', '内容', '登録日時']);

            foreach ($contacts as $c) {
                $row = [
                    $sanitize($c->last_name . ' ' . $c->first_name),
                    ['', '男性', '女性', 'その他'][$c->gender] ?? '-',
                    $sanitize($c->email),
                    optional($c->category)->content,
                    $sanitize($c->tel),
                    $sanitize(trim(($c->address ?? '') . ' ' . ($c->building ?? ''))),
                    $sanitize($c->detail),
                    optional($c->created_at)->format('Y-m-d H:i:s'),
                ];
                $writeCsvLine($fp, $row);
            }

            fclose($fp);
        });

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

        return $response;
    }
}