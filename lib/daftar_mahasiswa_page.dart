import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'edit_mahasiswa_page.dart';


class DaftarMahasiswaPage extends StatefulWidget {
  @override
  _DaftarMahasiswaPageState createState() => _DaftarMahasiswaPageState();
}

class _DaftarMahasiswaPageState extends State<DaftarMahasiswaPage> {
void _confirmDelete(String id) {
  showDialog(
    context: context,
    builder: (context) {
      return AlertDialog(
        title: const Text("Hapus Data"),
        content: const Text("Apakah Anda yakin ingin menghapus data ini?"),
        actions: [
          TextButton(
            child: const Text("Batal"),
            onPressed: () => Navigator.pop(context),
          ),
          TextButton(
            child: const Text("Hapus", style: TextStyle(color: Colors.red)),
            onPressed: () {
              Navigator.pop(context);
              _deleteMahasiswa(id);
            },
          ),
        ],
      );
    },
  );
}
Future<void> _deleteMahasiswa(String id) async {
  final url = Uri.parse("http://192.168.1.51/flutter_api/delete_mahasiswa.php");

  final response = await http.post(url, body: {
    "id": id,
  });

  print("DELETE RESPONSE: ${response.body}");

  final result = jsonDecode(response.body);

  if (result['status'] == "success") {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text("Data berhasil dihapus")),
    );
    fetchMahasiswa(); // refresh list
  } else {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text("Gagal menghapus: ${result['message']}")),
    );
  }
}

  List mahasiswa = [];
  bool loading = true;

  @override
  void initState() {
    super.initState();
    fetchMahasiswa();
  }

  Future<void> fetchMahasiswa() async {
    final url = Uri.parse("http://127.0.0.1/flutter_api/get_mahasiswa.php");

    final response = await http.get(url);

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);

      setState(() {
        mahasiswa = data['data'];
        loading = false;
      });
    } else {
      setState(() => loading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Gagal mengambil data mahasiswa")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Daftar Mahasiswa"),
        backgroundColor: Colors.teal,
      ),

      body: loading
          ? const Center(child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: mahasiswa.length,
              itemBuilder: (context, index) {
                final m = mahasiswa[index];

return Card(
  margin: const EdgeInsets.all(8),
  child: ListTile(
    leading: CircleAvatar(
      radius: 28,
      backgroundImage: m['foto_url'] != ""
          ? NetworkImage(m['foto_url'])
          : null,
      child: m['foto_url'] == "" ? const Icon(Icons.person) : null,
    ),

    title: Text(
      m['nama'],
      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
    ),

    subtitle: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text("Email: ${m['email']}"),
        Text("Umur: ${m['umur']} tahun"),
        Text("Gender: ${m['gender']} • Status: ${m['status']}"),
        Text("Hobi: ${ (m['hobi'] as List).join(', ') }"),
      ],
    ),

    trailing: Row(
  mainAxisSize: MainAxisSize.min,
  children: [
    IconButton(
      icon: const Icon(Icons.edit, color: Colors.blue),
      onPressed: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => EditMahasiswaPage(data: m),
          ),
        ).then((_) => fetchMahasiswa()); // refresh list setelah edit
      },
    ),
    IconButton(
      icon: const Icon(Icons.delete, color: Colors.red),
      onPressed: () {
        _confirmDelete(m['id']);
      },
    ),
  ],
),

  ),
);


              },
            ),
    );
  }
}
