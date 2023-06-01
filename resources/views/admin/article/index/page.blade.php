input

<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th class="col-lg-1">Tiêu đề</th>
            <th class="col-lg-1">Chuyên mục</th>
            <th class="col-lg-1">Đường link (SLUG)</th>
            <th class="col-lg-1">Ảnh đại diện</th>
            <th class="col-lg-2">Nội dung</th>
            @if($conditionView === 'index')
                <th class="col-lg-1">Số thứ tự</th>
                <th class="col-lg-1">Trạng thái</th>
                <th class="col-lg-1">Ngày tạo</th>
                <th class="col-lg-1">Ngày cập nhật</th>
            @endif
            <th class="col-lg-1"></th>
            <th class="col-lg-1"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($articles as $article)
            <tr class="article-row" data-id="{{ $article->id }}">
                <td class="col-lg-1">{{ $article->title }}</td>
                <td class="col-lg-1">{{ $article->category->title }}</td>
                <td class="col-lg-1">{{ $article->slug }}</td>
                <td class="col-lg-1"><img src="{{ asset($article->image) }}" alt="ảnh đại diện" style="height: 3rem">
                </td>
                <td class="col-lg-2">{{ Str::limit(strip_tags($article->content), 100) }}</td>
                @if($conditionView === 'index')
                    @if($article->status == 1)
                        <td class="col-lg-1"><input type="input" value="{{ $article->order_number }}"
                                                    style="width: 3rem"
                                                    class="order-number status-{{$article->status}}"></td>
                    @elseif($article->status == 0)
                        <td></td>
                    @endif
                    <td class="col-lg-1 ">
                        @if($article->status == 1)
                            Công khai
                        @elseif($article->status == 0)
                            Bản nháp
                        @endif
                    </td>
                    <td class="col-lg-1">{{ $article->created_at }}</td>
                    <td class="col-lg-1">{{ $article->updated_at }}</td>
                @endif
                @if($conditionView === 'index')
                    <td class="col-lg-1">
                        <a href="{{route('articles.edit',[ $article->id ])}}" class="btn btn-warning">
                            Sửa
                            <i class="fa fa-magic" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td class="col-lg-1">
                        <form action="{{route('articles.destroy',[ $article->id ])}}" method="POST" id="delete-article">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                @elseif($conditionView === 'trash')
                    <td class="col-lg-1">
                        <a href="{{route('articles.restore',[ $article->id ])}}" class="btn btn-info restore">
                            Khôi phục
                            <i class="fa fa-magic" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td class="col-lg-1">
                        <form action="{{route('articles.forceDestroy',[ $article->id ])}}" method="POST"
                              id="force-delete-article">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa vĩnh viễn
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                @endif
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center">
    {!! $articles->appends(request()->query())->links() !!}
</div>
<script>
    $(document).on('change', 'input.order-number', function (event) {
        let id = $(this).closest('tr.article-row').data('id');
        let currentOrderNumber = $(this).val();
        // Gửi yêu cầu AJAX để kiểm tra và cập nhật order_number
        $.ajax({
            type: 'POST',
            url: '{{ route("admin.updateOrderNumber") }}',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                orderNumber: currentOrderNumber
            },
            success: function (response) {
                if (response.exists) {
                    alert('Giá trị đã tồn tại trong cơ sở dữ liệu. Vui lòng nhập giá trị khác.');
                    $(this).val(response.currentOrderNumber);
                } else {
                    // Gửi yêu cầu AJAX để cập nhật order_number
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("admin.updateOrderNumber") }}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            orderNumber: currentOrderNumber
                        },
                        success: function (response) {
                            if (response.success) {
                                $(this).val(response.newOrderNumber);
                            } else {
                                alert(response.message);
                                $(this).val(response.olderNumber);
                            }
                        }.bind(this)
                    });
                }
            }.bind(this)
        });
    });
    $(document).ready(function () {
        $('form#delete-article').submit(function (e) {
            e.preventDefault();

            if (confirm("Bạn có muốn xóa bài viết này không ? Nó sẽ được đưa vào thùng rác ")) {
                var url = $(this).attr('action');
                var token = $(this).find('input[name="_token"]').val();
                var row = $(this).closest('.article-row');
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: token
                    },
                    success: function () {
                        // Xử lý khi xóa thành công
                        console.log(row);
                        row.fadeOut();
                    },
                    error: function (xhr, status, error) {
                        // Xử lý khi xóa thất bại
                    }
                });
            }
        });
        $('form#force-delete-article').submit(function (e) {
            e.preventDefault();

            if (confirm("Bạn có muốn xóa vĩnh viễn bài viết này và không hối tiếc ?")) {
                var url = $(this).attr('action');
                var token = $(this).find('input[name="_token"]').val();
                var row = $(this).closest('.article-row');
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: token
                    },
                    success: function () {
                        // Xử lý khi xóa thành công
                        console.log(row);
                        row.fadeOut();
                    },
                    error: function (xhr, status, error) {
                        // Xử lý khi xóa thất bại
                    }
                });
            }
        });
        $('.restore').click(function (e) {
            e.preventDefault();

            if (confirm("Bạn có muốn phục hồi bài viết này không ?")) {
                var url = $(this).attr('href');
                var row = $(this).closest('.article-row');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function () {
                        // Xử lý khi xóa thành công
                        console.log(row);
                        row.fadeOut();
                    },
                    error: function (xhr, status, error) {
                        // Xử lý khi xóa thất bại
                    }
                });
            }
        });
    });

</script>

{{--<script src="{{ asset('js/page.js') }}"></script>--}}

