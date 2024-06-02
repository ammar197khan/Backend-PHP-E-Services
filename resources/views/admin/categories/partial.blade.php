@foreach ($categories as $subSubCat)
  <tr name="sub_{{$subSubCat->parent_id}}" grand="{{ $subSubCat->parent->parent->id }}">
    <td style="border-right-style: dotted; border-right-width:thin;"></td>
    <td class="text-center">#{{ $subSubCat->id }}</td>
    <td class="text-center">{{ $subSubCat->en_name }}</td>
    <td class="text-center">{{ $subSubCat->ar_name }}</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">-</td>
    <td class="text-center">
      @if(admin()->hasPermissionTo('Edit category'))
        <a title="Edit" href="{{ route('admin.categories.edit', $subSubCat->id) }}"><i class="fa fa-edit"></i></a>
      @endif
      @if(admin()->hasPermissionTo('Delete category'))
        <a onclick="showDeleteModal({{$subSubCat->id}})" title="Delete" href="#" class="mb-control"><i class="fa fa-trash"></i></a>
      @endif
    </td>
  </tr>
@endforeach
