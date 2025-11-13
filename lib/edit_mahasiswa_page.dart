import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class EditMahasiswaPage extends StatefulWidget {
  final Map data;

  const EditMahasiswaPage({Key? key, required this.data}) : super(key: key);

  @override
  _EditMahasiswaPageState createState() => _EditMahasiswaPageState();
}

class _EditMahasiswaPageState extends State<EditMahasiswaPage> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nama;
  late TextEditingController _email;
  late TextEditingController _umur;

  String? gender;
  String? status;
  List<String> selectedHobi = [];

  File? imageFile;
  final picker = ImagePicker();

  @override
  void initState() {
    super.initState();

    _nama = TextEditingController(text: widget.data['nama']);
    _email = TextEditingController(text: widget.data['email']);
    _umur = TextEditingController(text: widget.data['umur'].toString());

    gender = widget.data['gender'];
    status = widget.data['status'];

    selectedHobi = List<String>.from(widget.data['hobi']);
  }

  Future pickImage() async {
    final picked = await picker.pickImage(source: ImageSource.gallery);
    if (picked != null) {
      setState(() {
        imageFile = File(picked.path);
      });
    }
  }

  Future updateData() async {
    var url = Uri.parse("http://192.168.1.51/flutter_api/update_mahasiswa.php");

    var request = http.MultipartRequest("POST", url);

    request.fields['id'] = widget.data['id'].toString();
    request.fields['nama'] = _nama.text;
    request.fields['email'] = _email.text;
    request.fields['umur'] = _umur.text;
    request.fields['gender'] = gender!;
    request.fields['status'] = status!;
    request.fields['hobi'] = jsonEncode(selectedHobi);

    if (imageFile != null) {
      request.files.add(await http.MultipartFile.fromPath(
        'foto',
        imageFile!.path,
      ));
    }

    var response = await request.send();
    var respStr = await response.stream.bytesToString();

    print("Response Update: $respStr");

    final data = jsonDecode(respStr);

    if (data['status'] == "success") {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Data berhasil diperbarui")),
      );
      Navigator.pop(context);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Gagal: ${data['message']}")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Edit Mahasiswa"),
        backgroundColor: Colors.teal,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              // FOTO
              Center(
                child: GestureDetector(
                  onTap: pickImage,
                  child: CircleAvatar(
                    radius: 60,
                    backgroundImage: imageFile != null
                        ? FileImage(imageFile!)
                        : (widget.data['foto_url'] != ""
                            ? NetworkImage(widget.data['foto_url'])
                            : null) as ImageProvider?,
                    child: imageFile == null && widget.data['foto_url'] == ""
                        ? const Icon(Icons.camera_alt, size: 40)
                        : null,
                  ),
                ),
              ),
              const SizedBox(height: 20),

              TextFormField(
                controller: _nama,
                decoration: const InputDecoration(labelText: "Nama"),
              ),
              TextFormField(
                controller: _email,
                decoration: const InputDecoration(labelText: "Email"),
              ),
              TextFormField(
                controller: _umur,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(labelText: "Umur"),
              ),

              const SizedBox(height: 10),
              const Text("Gender"),
              DropdownButton(
                isExpanded: true,
                value: gender,
                items: ["Laki-laki", "Perempuan"]
                    .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                    .toList(),
                onChanged: (v) => setState(() => gender = v),
              ),

              const SizedBox(height: 10),
              const Text("Status"),
              DropdownButton(
                isExpanded: true,
                value: status,
                items: ["Aktif", "Cuti"]
                    .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                    .toList(),
                onChanged: (v) => setState(() => status = v),
              ),

              const SizedBox(height: 20),
              const Text("Hobi:", style: TextStyle(fontSize: 16)),

              ...["Membaca", "Menulis", "Olahraga", "Gaming"].map((h) {
                return CheckboxListTile(
                  title: Text(h),
                  value: selectedHobi.contains(h),
                  onChanged: (val) {
                    setState(() {
                      if (val == true) {
                        selectedHobi.add(h);
                      } else {
                        selectedHobi.remove(h);
                      }
                    });
                  },
                );
              }).toList(),

              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: updateData,
                child: const Text("Simpan Perubahan"),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
