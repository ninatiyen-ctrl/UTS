import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'dart:io';
import 'package:image_picker/image_picker.dart';
import 'package:http/http.dart' as http;
import 'result_page.dart';

class FormPage extends StatefulWidget {
  @override
  _FormPageState createState() => _FormPageState();
}

class _FormPageState extends State<FormPage> {
  final _formKey = GlobalKey<FormState>();
  final _namaController = TextEditingController();
  final _emailController = TextEditingController();
  final _umurController = TextEditingController();

  File? _image;
  XFile? _webImage;
  final ImagePicker _picker = ImagePicker();

  String? _selectedGender;
  String? _statusMahasiswa;
  Map<String, bool> _hobi = {
    'Membaca': false,
    'Menulis': false,
    'Olahraga': false,
    'Gaming': false,
  };

  // 🧩 Fungsi ambil gambar (kamera / galeri)
  Future<void> _pickImage() async {
    final XFile? pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() {
        if (kIsWeb) {
          _webImage = pickedFile;
        } else {
          _image = File(pickedFile.path);
        }
      });
    }
  }

  // 🧩 Fungsi kirim data (pakai MultipartRequest agar bisa kirim foto)
  Future<void> _submitForm() async {
    if (_formKey.currentState!.validate() && _statusMahasiswa != null) {
      List<String> selectedHobi = _hobi.entries
          .where((entry) => entry.value)
          .map((entry) => entry.key)
          .toList();

      var uri = Uri.parse("http://192.168.1.51/flutter_api/save_biodata.php"); // ✅ ganti IP laptop kamu

      var request = http.MultipartRequest('POST', uri);

      // Tambahkan data ke form
      request.fields['nama'] = _namaController.text;
      request.fields['email'] = _emailController.text;
      request.fields['umur'] = _umurController.text;
      request.fields['gender'] = _selectedGender ?? '';
      request.fields['status'] = _statusMahasiswa ?? '';
      request.fields['hobi'] = jsonEncode(selectedHobi);

      // Kirim file
      if (!kIsWeb && _image != null) {
        request.files.add(await http.MultipartFile.fromPath('image', _image!.path));
      } else if (kIsWeb && _webImage != null) {
        final bytes = await _webImage!.readAsBytes();
        request.files.add(http.MultipartFile.fromBytes(
          'image',
          bytes,
          filename: _webImage!.name,
        ));
      }

      try {
        var response = await request.send();
        var responseBody = await response.stream.bytesToString();
        print("Respons dari PHP: $responseBody");

        final data = jsonDecode(responseBody);

        if (data["status"] == "success") {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
            content: Text("✅ Data berhasil dikirim!"),
            backgroundColor: Colors.green,
          ));

          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ResultPage(
                nama: _namaController.text,
                email: _emailController.text,
                umur: _umurController.text,
                gender: _selectedGender!,
                status: _statusMahasiswa!,
                hobi: selectedHobi,
              ),
            ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
            content: Text("❌ Gagal: ${data["message"]}"),
            backgroundColor: Colors.redAccent,
          ));
        }
      } catch (e) {
        print("Error: $e");
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text("Terjadi kesalahan koneksi ke server."),
          backgroundColor: Colors.redAccent,
        ));
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text("Lengkapi semua data terlebih dahulu!"),
        backgroundColor: Colors.redAccent,
      ));
    }
  }

  // 🧩 Reset form
  void _resetForm() {
    _namaController.clear();
    _emailController.clear();
    _umurController.clear();
    _selectedGender = null;
    _statusMahasiswa = null;
    _hobi.updateAll((key, value) => false);
    _image = null;
    _webImage = null;
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Form Biodata Lengkap'),
        backgroundColor: Colors.teal,
      ),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              GestureDetector(
                onTap: _pickImage,
                child: CircleAvatar(
                  radius: 60,
                  backgroundColor: Colors.teal.shade100,
                  backgroundImage: _image != null
                      ? FileImage(_image!)
                      : (_webImage != null
                          ? NetworkImage(_webImage!.path)
                          : null) as ImageProvider?,
                  child: _image == null && _webImage == null
                      ? Icon(Icons.camera_alt, size: 40, color: Colors.teal)
                      : null,
                ),
              ),
              SizedBox(height: 10),
              Text("Tap untuk memilih foto"),

              // Nama
              SizedBox(height: 20),
              TextFormField(
                controller: _namaController,
                decoration: InputDecoration(labelText: 'Nama'),
                validator: (v) => v!.isEmpty ? 'Nama wajib diisi' : null,
              ),
              SizedBox(height: 10),

              // Email
              TextFormField(
                controller: _emailController,
                decoration: InputDecoration(labelText: 'Email'),
                validator: (v) => v!.isEmpty ? 'Email wajib diisi' : null,
              ),
              SizedBox(height: 10),

              // Umur
              TextFormField(
                controller: _umurController,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(labelText: 'Umur'),
                validator: (v) => v!.isEmpty ? 'Umur wajib diisi' : null,
              ),
              SizedBox(height: 10),

              // Gender
              DropdownButtonFormField<String>(
                value: _selectedGender,
                hint: Text('Pilih Jenis Kelamin'),
                items: ['Laki-laki', 'Perempuan']
                    .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                    .toList(),
                onChanged: (v) => setState(() => _selectedGender = v),
                validator: (v) => v == null ? 'Pilih jenis kelamin' : null,
              ),
              SizedBox(height: 10),

              // Status Mahasiswa
              Text("Status Mahasiswa:"),
              RadioListTile<String>(
                title: Text("Aktif"),
                value: "Aktif",
                groupValue: _statusMahasiswa,
                onChanged: (v) => setState(() => _statusMahasiswa = v),
              ),
              RadioListTile<String>(
                title: Text("Cuti"),
                value: "Cuti",
                groupValue: _statusMahasiswa,
                onChanged: (v) => setState(() => _statusMahasiswa = v),
              ),
              SizedBox(height: 10),

              // Hobi
              Text("Pilih Hobi:"),
              Column(
                children: _hobi.keys.map((key) {
                  return CheckboxListTile(
                    title: Text(key),
                    value: _hobi[key],
                    onChanged: (val) =>
                        setState(() => _hobi[key] = val ?? false),
                  );
                }).toList(),
              ),

              // Tombol
              SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      icon: Icon(Icons.send),
                      label: Text("Kirim Data"),
                      onPressed: _submitForm,
                      style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.teal,
                          padding: EdgeInsets.symmetric(vertical: 14)),
                    ),
                  ),
                  SizedBox(width: 10),
                  Expanded(
                    child: ElevatedButton.icon(
                      icon: Icon(Icons.refresh),
                      label: Text("Reset"),
                      onPressed: _resetForm,
                      style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.redAccent,
                          padding: EdgeInsets.symmetric(vertical: 14)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
