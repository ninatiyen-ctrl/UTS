import 'dart:io';
import 'package:flutter/material.dart';
import 'daftar_mahasiswa_page.dart';

/// List global menyimpan semua mahasiswa
List<Map<String, dynamic>> daftarMahasiswa = [];

class ResultPage extends StatelessWidget {
  final String nama;
  final String email;
  final String umur;
  final String gender;
  final String status;
  final List<String> hobi;
  final File? image;

  const ResultPage({
    Key? key,
    required this.nama,
    required this.email,
    required this.umur,
    required this.gender,
    required this.status,
    required this.hobi,
    this.image,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    
    /// Simpan data mahasiswa ke list global
    daftarMahasiswa.add({
      'nama': nama,
      'email': email,
      'umur': umur,
      'gender': gender,
      'status': status,
      'hobi': hobi,
    });

    return Scaffold(
      backgroundColor: Colors.teal.shade50,
      appBar: AppBar(
        title: const Text('Hasil Input'),
        centerTitle: true,
        backgroundColor: Colors.teal,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16.0),
          child: Center(
            child: Card(
              elevation: 10,
              margin: const EdgeInsets.all(8),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Icon(
                      Icons.assignment_turned_in,
                      size: 70,
                      color: Colors.teal.shade700,
                    ),
                    const SizedBox(height: 20),
                    Text(
                      'Data yang Anda Masukkan',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: Colors.teal.shade800,
                      ),
                    ),
                    const SizedBox(height: 20),

                    if (image != null)
                      ClipRRect(
                        borderRadius: BorderRadius.circular(8),
                        child: Image.file(
                          image!,
                          height: 150,
                          fit: BoxFit.cover,
                        ),
                      ),

                    const SizedBox(height: 20),
                    ListTile(
                      leading: const Icon(Icons.person, color: Colors.teal),
                      title: const Text('Nama Lengkap'),
                      subtitle: Text(nama),
                    ),
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.email, color: Colors.teal),
                      title: const Text('Email'),
                      subtitle: Text(email),
                    ),
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.cake, color: Colors.teal),
                      title: const Text('Umur'),
                      subtitle: Text('$umur tahun'),
                    ),
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.wc, color: Colors.teal),
                      title: const Text('Jenis Kelamin'),
                      subtitle: Text(gender),
                    ),
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.school, color: Colors.teal),
                      title: const Text('Status Mahasiswa'),
                      subtitle: Text(status),
                    ),
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.favorite, color: Colors.teal),
                      title: const Text('Hobi'),
                      subtitle: Text(
                        hobi.isNotEmpty
                            ? hobi.join(', ')
                            : 'Tidak ada hobi yang dipilih',
                      ),
                    ),
                    const SizedBox(height: 30),

                    /// Tombol kembali
                    ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.teal,
                        padding: const EdgeInsets.symmetric(
                            horizontal: 24, vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      icon: const Icon(Icons.arrow_back),
                      label: const Text('Kembali ke Form'),
                      onPressed: () => Navigator.pop(context),
                    ),

                    const SizedBox(height: 10),

                    /// Tombol lihat daftar mahasiswa
                   ElevatedButton.icon(
  style: ElevatedButton.styleFrom(
    backgroundColor: Colors.blueGrey,
    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(10),
    ),
  ),
  icon: const Icon(Icons.list),
  label: const Text('Lihat Daftar Mahasiswa'),
  onPressed: () {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => DaftarMahasiswaPage(),
      ),
    );
  },
),

                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
