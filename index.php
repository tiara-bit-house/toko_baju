<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
    <?php
    $conn = new mysqli('localhost', 'root', '', 'toko_baju');

    if (isset($_POST['tambahBaju'])) {
        $id_transaksi = $_POST['id_transaksi'];
        $id_baju = $_POST['id_baju'];

        $select_baju = $conn->query("SELECT harga_baju FROM baju WHERE id_baju = $id_baju");
        $harga_baju = $select_baju->fetch_object()->harga_baju;


        $tambahDetailStmt = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi, id_baju, harga_baju) VALUES (?,?,?)");
        $tambahDetailStmt->bind_param('iid', $id_transaksi, $id_baju, $harga_baju);

        $tambahDetailStmt->execute();

        $tambahTotalHargaStmt = $conn->prepare("UPDATE transaksi SET total_harga = total_harga + ? WHERE id_transaksi = ?");
        $tambahTotalHargaStmt->bind_param('di', $harga_baju, $id_transaksi);

        $tambahTotalHargaStmt->execute();
    }

    $stmt = $conn->query('SELECT * FROM transaksi WHERE id_transaksi = 1');
    $transaksi = $stmt->fetch_object();

    $stmt2 = $conn->query(
        "SELECT detail_transaksi.id_transaksi, detail_transaksi.harga_baju, baju.nama 
        FROM detail_transaksi 
        JOIN baju ON baju.id_baju = detail_transaksi.id_baju
        WHERE detail_transaksi.id_transaksi = 1"
    );
    $details = $stmt2->fetch_all();


    $stmt3 = $conn->query("SELECT * FROM baju");
    $cloths = $stmt3->fetch_all();

    ?>
</head>

<body>

    <table border="1" style="width: 100vw;">
        <thead>
            <tr>
                <th>Transaksi ID</th>
                <th>Date</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $transaksi->id_transaksi ?></td>
                <td><?php echo date('d M, Y') ?></td>
                <td>Rp<?php echo $transaksi->total_harga ?></td>
            </tr>
            <tr>
                <td>---</td>
                <td>---</td>
                <td>---</td>
            </tr>
            <?php foreach ($details as $i => $detail): ?>
                <tr>
                    <td><?php echo $i + 1 ?></td>
                    <td><?php echo $detail[1] ?></td>
                    <td><?php echo $detail[2] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <hr>
    <h1>Masukkan baju yang dibeli</h1>
    <form action="index.php" method="POST">
        <input type="hidden" value="1" name="id_transaksi">
        <select name="id_baju">
            <option value="" hidden>Select baju</option>
            <?php foreach ($cloths as $cloth): ?>
                <option value="<?php echo $cloth['0'] ?>"><?php echo $cloth['1'] ?></option>
            <?php endforeach ?>
        </select>
        <button type="submit" name="tambahBaju">Tambah ke transaksi</button>
    </form>

</body>

</html>