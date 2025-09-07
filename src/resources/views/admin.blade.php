@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="admin-page">
  <h2 class="admin-title">Admin</h2>

  <form action="{{ url('/admin') }}" method="GET" class="admin-filter" role="search">
    @php
      $keyword = request('keyword','');
      $gender  = request('gender','');
      $type    = request('type','');
      $date    = request('date','');
    @endphp

    <div class="admin-filter__row">
      <div class="admin-filter__inputs">
        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="名前やメールアドレスを入力してください" class="admin-input" aria-label="キーワード">
        <select name="gender" class="admin-input admin-input--select" aria-label="性別">
          <option value="">性別</option>
          <option value="male"   @selected($gender==='male')>男性</option>
          <option value="female" @selected($gender==='female')>女性</option>
          <option value="other"  @selected($gender==='other')>その他</option>
        </select>
        <select name="type" class="admin-input admin-input--select" aria-label="お問い合わせの種類">
          <option value="">お問い合わせの種類</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string)$type === (string)$cat->id ? 'selected' : '' }}>
              {{ $cat->content }}
            </option>
          @endforeach
        </select>
        <input type="date" name="date" value="{{ $date }}" class="admin-input" aria-label="年/月/日">
      </div>

      <div class="admin-filter__buttons">
        <button type="submit" class="admin-btn admin-btn--primary">検索</button>
        <a href="{{ url('/admin') }}" class="admin-btn admin-btn--reset">リセット</a>
      </div>
    </div>
  </form>

  @php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $contacts */
    $page = $contacts->currentPage();
    $last = $contacts->lastPage();
    $q = request()->query();
  @endphp

  <div class="admin-toolbar">
    <a href="{{ route('admin.export', $q) }}" class="admin-btn admin-btn--ghost">エクスポート</a>
  </div>

  <div class="admin-pager">
    <ul class="pagination">
      <li class="{{ $page === 1 ? 'disabled' : '' }}">
        @if ($page === 1)
          <span>&lt;</span>
        @else
          <a href="{{ $contacts->appends($q)->url($page-1) }}">&lt;</a>
        @endif
      </li>

      @php
        $window = 5;
        $half   = intdiv($window, 2);
        $start  = max(1, $page - $half);
        $end    = min($last, $start + $window - 1);
        $start  = max(1, $end - $window + 1);
      @endphp

      @for ($i = $start; $i <= $end; $i++)
        <li class="{{ $i === $page ? 'active' : '' }}">
          @if ($i === $page)
            <span>{{ $i }}</span>
          @else
            <a href="{{ $contacts->appends($q)->url($i) }}">{{ $i }}</a>
          @endif
        </li>
      @endfor

      <li class="{{ $page === $last ? 'disabled' : '' }}">
        @if ($page === $last)
          <span>&gt;</span>
        @else
          <a href="{{ $contacts->appends($q)->url($page+1) }}">&gt;</a>
        @endif
      </li>
    </ul>
  </div>

  <div class="admin-table__wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 12rem;">お名前</th>
          <th style="width: 8rem;">性別</th>
          <th style="width: 18rem;">メールアドレス</th>
          <th>お問い合わせの種類</th>
          <th style="width: 8rem;">詳細</th>
          <th style="width: 8rem;">削除</th>
        </tr>
      </thead>

      <tbody>
        @forelse($contacts as $c)
          <tr>
            <td>{{ $c->last_name }} {{ $c->first_name }}</td>
            <td>{{ ['','男性','女性','その他'][$c->gender] ?? '-' }}</td>
            <td class="col-email">{{ $c->email }}</td>
            <td>{{ optional($c->category)->content }}</td>
            <td>
              <button
                type="button"
                class="admin-link js-open-modal"
                data-id="{{ $c->id }}"
                data-name="{{ $c->last_name }} {{ $c->first_name }}"
                data-gender="{{ ['','男性','女性','その他'][$c->gender] ?? '-' }}"
                data-email="{{ $c->email }}"
                data-tel="{{ $c->tel }}"
                data-address="{{ $c->address }}"
                data-building="{{ $c->building }}"
                data-type="{{ optional($c->category)->content }}"
                data-body="{{ $c->detail }}"
              >
                詳細
              </button>
            </td>
            <td>
              <form class="js-delete-form" action="{{ route('admin.destroy', $c) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-link admin-link--danger">削除</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="admin-table__empty">データがありません</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="admin-modal__overlay" hidden></div>
  <div class="admin-modal__dialog" role="dialog" aria-modal="true" hidden>
    <button type="button" class="admin-modal__close" aria-label="閉じる">×</button>

    <div class="admin-modal__body">
      <dl class="admin-modal__rows">
        <div class="admin-modal__row"><dt>お名前</dt><dd id="m-name"></dd></div>
        <div class="admin-modal__row"><dt>性別</dt><dd id="m-gender"></dd></div>
        <div class="admin-modal__row"><dt>メールアドレス</dt><dd id="m-email"></dd></div>
        <div class="admin-modal__row"><dt>電話番号</dt><dd id="m-tel"></dd></div>
        <div class="admin-modal__row"><dt>住所</dt><dd id="m-address"></dd></div>
        <div class="admin-modal__row"><dt>建物名</dt><dd id="m-building"></dd></div>
        <div class="admin-modal__row"><dt>お問い合わせの種類</dt><dd id="m-type"></dd></div>
        <div class="admin-modal__row"><dt>お問い合わせ内容</dt><dd id="m-body"></dd></div>
      </dl>
    </div>

    <div class="admin-modal__footer">
      <button type="button" class="admin-modal__delete-btn">削除</button>
    </div>
  </div>
</div>

<form id="js-delete-form" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const overlay  = document.querySelector('.admin-modal__overlay');
  const dialog   = document.querySelector('.admin-modal__dialog');
  const closeBtn = document.querySelector('.admin-modal__close');
  const openBtns = document.querySelectorAll('.js-open-modal');
  const modalDel = document.querySelector('.admin-modal__delete-btn');
  const delForm  = document.getElementById('js-delete-form');

  let currentId = null;

  const F = {
    name: document.getElementById('m-name'),
    gender: document.getElementById('m-gender'),
    email: document.getElementById('m-email'),
    tel: document.getElementById('m-tel'),
    address: document.getElementById('m-address'),
    building: document.getElementById('m-building'),
    type: document.getElementById('m-type'),
    body: document.getElementById('m-body'),
  };

  const open  = () => { overlay.hidden = false; dialog.hidden = false; document.body.style.overflow = 'hidden'; };
  const close = () => { overlay.hidden = true;  dialog.hidden = true;  document.body.style.overflow = ''; };

  openBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      currentId = btn.dataset.id || null;
      F.name.textContent     = btn.dataset.name     || '';
      F.gender.textContent   = btn.dataset.gender   || '';
      F.email.textContent    = btn.dataset.email    || '';
      F.tel.textContent      = btn.dataset.tel      || '';
      F.address.textContent  = btn.dataset.address  || '';
      F.building.textContent = btn.dataset.building || '';
      F.type.textContent     = btn.dataset.type     || '';
      F.body.textContent     = btn.dataset.body     || '';
      open();
    });
  });

  overlay.addEventListener('click', close);
  closeBtn.addEventListener('click', close);
  window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !dialog.hidden) close(); });

  modalDel.addEventListener('click', () => {
    if (!currentId) return;
    if (confirm('このお問い合わせを削除します。よろしいですか？')) {
      delForm.action = `/admin/${currentId}`;
      delForm.submit();
    }
  });

  document.querySelectorAll('.js-delete-form').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (!confirm('このお問い合わせを削除します。よろしいですか？')) {
        e.preventDefault();
      }
    });
  });
});
</script>
@endsection