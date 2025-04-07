$(".owl-carousel").owlCarousel({
    loop: true,
    margin: 10,
    nav: false,
    dots: true,
    items: 1,
});
// Domain status
$(".status-confirm").on("click", function (event) {
    event.preventDefault();
    const url = $(this).attr("href");
    swal(
        {
            title: "Are you sure?",
            text: "Do you want to update domain request status?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false,
        },
        function () {
            window.location.href = url;
        }
    );
});

// Domain request reject
$(".domain-reject-btn").on("click", function (event) {
    event.preventDefault();
    let id = $(this).data("id");

    $("#domainRejectedModal form input[name='id']").val(id);
});
