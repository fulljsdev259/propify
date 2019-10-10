@extends('pdfs.layout')

@section('title')
    {{ __("models.resident.credentials_pdf.resident_credentials") }}
@endsection

@section('body')
<table>
  <tr>
    <td>
      <p id="resident-header">
        @lang("general.salutation_option." . $resident->title, [], $language)
        <br>
        <b>{{ $resident->first_name . ' ' . $resident->last_name }}</b>
      </p>

      @if ($resident->address)
       <p>
         {{ $resident->address->street }} {{ $resident->address->street_nr }}
         <br>
         {{ $resident->address->zip }} {{ $resident->address->city }}
       </p>
      @endif
    </td>
    <td id="real-estate">
      <img class="logo" src="{{ $settings->logo ? asset($settings->logo) : asset('images/logo3.png') }}"/>
      <p>{{ $settings->name }}</p>
      <p>
        {{ $settings->address->street }}<br />
        {{ $settings->address->zip }} {{ $settings->address->city }}
      </p>
      <p>
        @lang('models.resident.credentials_pdf.telephone', [], $language): {{ $settings->phone }}
        <br>
        @lang('models.resident.credentials_pdf.email', [], $language): {{ $settings->email }}</p>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <br>
      <br>
      <br>
      {{ $settings->address->city }}, {{now()->format('d.m.Y')}}
      <br>
      <br>
      <br>
      <b>@lang('models.resident.credentials_pdf.welcome', [], $language) {{ $settings->name }}</b>
      <br>
      <br>
      @if(\App\Models\Resident::TitleCompany == $resident->title)
        @lang('general.pdf_salutation.' . $resident->title, [], $language)
      @else
        @lang('general.pdf_salutation.' . $resident->title, ['name' => $resident->last_name], $language)
      @endif
      <br>
      <br>
      @lang('models.resident.credentials_pdf.content_1', [], $language)
      <br>
      <br>
      <p class="offer"><b>@lang('models.resident.credentials_pdf.offer', [], $language)</b></p>
      <ul class="offer">
        {!! __('models.resident.credentials_pdf.offers', [], $language) !!}
      </ul>

      <b>@lang('models.resident.credentials_pdf.register', [], $language)</b>
      <br>
      @lang('models.resident.credentials_pdf.content_2', [], $language)
      <br>
      <br>
    </td>
  </tr>
  <tr>
    <td>@lang("models.resident.credentials_pdf.link_application", [], $language)</td>
    <td>{{ url('/activate') }}</td>
  </tr>
  <tr>
    <td>{{ __("models.resident.credentials_pdf.code") }}</td>
    <td>{{ $code }}</td>
  </tr>
  <tr>
    <td>{{ __("models.resident.credentials_pdf.your_email") }}</td>
    <td>{{ $resident->user->email }}</td>
  </tr>
  <tr>
    <td colspan="4">
      <br>
      @lang('models.resident.credentials_pdf.content_3', [], $language)
      <br>
      <br>
      @lang('models.resident.credentials_pdf.content_4', [], $language)
      <br>
      <br>
      <br>
      @lang('models.resident.credentials_pdf.your_sincerely', [], $language)
      <br>
      @lang('models.resident.credentials_pdf.your_administration', [], $language)
    </td>
  </tr>
</table>
@endsection
