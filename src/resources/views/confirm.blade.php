@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
@endsection

@section('content')
<div class="confirm-page">
  <h2 class="confirm-title">Confirm</h2>

  @php
    $v = fn($k) => $inputs[$k] ?? '';
    $genderText = $v('gender');
    $tel = (trim($v('tel1')) !== '' || trim($v('tel2')) !== '' || trim($v('tel3')) !== '')
          ? ($v('tel1') . '-' . $v('tel2') . '-' . $v('tel3'))
          : '';
    $contentSanitized = nl2br(e(preg_replace('/^[\h　]+/u', '', $v('content'))));
  @endphp

  <form class="confirm-form" action="{{ url('/confirm') }}" method="post">
    @csrf

    <table class="confirm-table">
      <tbody>
        <tr>
          <th>お名前</th>
          <td>
            {{ $v('last_name') }}　{{ $v('first_name') }}
            <input type="hidden" name="last_name" value="{{ $v('last_name') }}">
            <input type="hidden" name="first_name" value="{{ $v('first_name') }}">
          </td>
        </tr>
        <tr>
          <th>性別</th>
          <td>
            {{ $genderText }}
            <input type="hidden" name="gender" value="{{ $genderText }}">
          </td>
        </tr>
        <tr>
          <th>メールアドレス</th>
          <td>
            {{ $v('email') }}
            <input type="hidden" name="email" value="{{ $v('email') }}">
          </td>
        </tr>
        <tr>
          <th>電話番号</th>
          <td>
            {{ $tel }}
            <input type="hidden" name="tel1" value="{{ $v('tel1') }}">
            <input type="hidden" name="tel2" value="{{ $v('tel2') }}">
            <input type="hidden" name="tel3" value="{{ $v('tel3') }}">
          </td>
        </tr>
        <tr>
          <th>住所</th>
          <td>
            {{ $v('address') }}
            <input type="hidden" name="address" value="{{ $v('address') }}">
          </td>
        </tr>
        <tr>
          <th>建物名</th>
          <td>
            {{ $v('building') }}
            <input type="hidden" name="building" value="{{ $v('building') }}">
          </td>
        </tr>
        <tr>
          <th>お問い合わせの種類</th>
          <td>
            {{ $inputs['type_label'] ?? '' }}
            <input type="hidden" name="type" value="{{ $inputs['type'] ?? '' }}">
          </td>
        </tr>
        <tr>
          <th>お問い合わせ内容</th>
          <td class="is-pre">
            {!! $contentSanitized !!}
            <input type="hidden" name="content" value="{{ $v('content') }}">
          </td>
        </tr>
      </tbody>
    </table>

    <div class="confirm-actions">
      <button type="submit" name="action" value="send" class="btn-submit">送信</button>
      <button type="submit" name="action" value="back" class="link-back">修正</button>
    </div>
  </form>
</div>
@endsection