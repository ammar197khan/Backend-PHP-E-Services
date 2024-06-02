@extends('dashboard.layouts.app')
@section('title')
  <form class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto">
    <div class="form-group mb-0">
      <div class="input-group input-group-alternative">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
        <input class="form-control" placeholder="Search" type="text">
      </div>
    </div>
  </form>
@endsection
@section('header')
  <!-- Form -->
  <form class="navbar-search navbar-search-dark ">
    <div class="row">
      <div class="col-md-10">
        <div class="form-group mb-0">
          <div class="input-group input-group-alternative">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input class="form-control" placeholder="Search" type="text">
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-primary btn-lg" style="border-radius:25px; width:170px">SEARCH</button>
      </div>
    </div>
  </form>
  <!-- User -->
@endsection


@section('content')
  <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3 class="mb-0">{{ __('language.Admins') }}</h3>
              </div>
              <div class="table-responsive">
                <table class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width:100px"></th>
                      <th scope="col">NAME</th>
                      <th scope="col">EMAIL</th>
                      <th scope="col">PHONE</th>
                      <th scope="col">ROLE</th>
                      <th scope="col">{{ __('language.Status') }}</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="Suspend" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <td><img alt="Image placeholder" src="../assets/img/theme/team-1-800x800.jpg" class="rounded-circle" style="height:50px"></td>
                        <td>Ahmed Salah</td>
                        <td>admin@admin.com</td>
                        <td>0022146541364</td>
                        <td>Super Admin</td>
                        <td>
                          <span class="badge badge-dot mr-4">
                            <i class="bg-warning"></i> pending
                          </span>
                        </td>
                        <td>
                          <a href="#"><i title="Show" style="font-size:15px" class="fas fa-eye"></i></a>
                          <a href="#"><i title="Edit" style="font-size:15px" class="fas fa-edit"></i></a>
                          <a href="#"><i title="{{ __('language.Suspend') }}" style="font-size:15px" class="fas fa-lock"></i></a>
                        </td>
                      </tr>

                  </tbody>
                </table>
              </div>
              <div class="card-footer py-4">
                <nav aria-label="...">
                  <ul class="pagination justify-content-end mb-0">
                    <li class="page-item disabled">
                      <a class="page-link" href="#" tabindex="-1">
                        <i class="fas fa-angle-left"></i>
                        <span class="sr-only">Previous</span>
                      </a>
                    </li>
                    <li class="page-item active">
                      <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                      <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                      <a class="page-link" href="#">
                        <i class="fas fa-angle-right"></i>
                        <span class="sr-only">Next</span>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
@endsection
