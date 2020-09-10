@extends('layouts.layout')
@section('content')
    <spinner-component v-if="spinner"></spinner-component>
    <list-component></list-component>
@stop