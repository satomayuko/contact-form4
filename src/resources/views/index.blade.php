@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="contact-form__content">
  <div class="contact-form__heading">
    <h2>Contact</h2>
  </div>

  <form class="form" action="{{ url('/confirm') }}" method="post"novalidate>
    @csrf

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">お名前</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content form__group-content--name">
        <input type="text" name="last_name" placeholder="例: 山田" value="{{ old('last_name') }}">
        <input type="text" name="first_name" placeholder="例: 太郎" value="{{ old('first_name') }}">
      </div>
      <div class="form__error">
        @error('last_name') {{ $message }} @enderror
        @error('first_name') {{ $message }} @enderror
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">性別</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content">
        <label><input type="radio" name="gender" value="男性"  {{ old('gender', '男性') === '男性' ? 'checked' : '' }}> 男性</label>
        <label><input type="radio" name="gender" value="女性"  {{ old('gender') === '女性' ? 'checked' : '' }}> 女性</label>
        <label><input type="radio" name="gender" value="その他" {{ old('gender') === 'その他' ? 'checked' : '' }}> その他</label>
      </div>
      <div class="form__error">@error('gender') {{ $message }} @enderror</div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">メールアドレス</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content">
        <input type="email" name="email" placeholder="例: test@example.com" value="{{ old('email') }}">
      </div>
      <div class="form__error">@error('email') {{ $message }} @enderror</div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">電話番号</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content form__group-content--tel">
        <input type="text" name="tel1" value="{{ old('tel1') }}" maxlength="5" inputmode="numeric" pattern="[0-9]*">
        <span class="hyphen">-</span>
        <input type="text" name="tel2" value="{{ old('tel2') }}" maxlength="5" inputmode="numeric" pattern="[0-9]*">
        <span class="hyphen">-</span>
        <input type="text" name="tel3" value="{{ old('tel3') }}" maxlength="5" inputmode="numeric" pattern="[0-9]*">
      </div>
      <div class="form__error">@error('tel') {{ $message }} @enderror</div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">住所</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content">
        <input type="text" name="address" placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3" value="{{ old('address') }}">
      </div>
      <div class="form__error">@error('address') {{ $message }} @enderror</div>
    </div>

    <div class="form__group">
      <div class="form__group-title"><span class="form__label--item">建物名</span></div>
      <div class="form__group-content">
        <input type="text" name="building" placeholder="例: 千駄ヶ谷マンション101" value="{{ old('building') }}">
      </div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">お問い合わせの種類</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content">
        <select name="type">
          <option value="">選択してください</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string) old('type') === (string) $cat->id ? 'selected' : '' }}>
              {{ $cat->content }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="form__error">@error('type') {{ $message }} @enderror</div>
    </div>

    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">お問い合わせ内容</span><span class="form__label--required">※</span>
      </div>
      <div class="form__group-content">
        <textarea name="content" placeholder="お問い合わせ内容をご記載ください" maxlength="120">{{ old('content') }}</textarea>
      </div>
      <div class="form__error">@error('content') {{ $message }} @enderror</div>
    </div>

    <div class="form__button">
      <button class="form__button-submit" type="submit">確認画面</button>
    </div>
  </form>
</div>
@endsection