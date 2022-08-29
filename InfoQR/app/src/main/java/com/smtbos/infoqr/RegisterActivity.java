package com.smtbos.infoqr;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONObject;

public class RegisterActivity extends AppCompatActivity {

    private SharedPreferences sharedPreferences;
    private SharedPreferences.Editor editor;

    private RequestQueue requestQueue;

    private EditText txt_name, txt_username, txt_password, txt_mobile, txt_email, txt_city, txt_address;
    private Button btn_register, btn_back_to_login;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        sharedPreferences = getSharedPreferences("InfoQR", MODE_PRIVATE);
        editor = sharedPreferences.edit();

        requestQueue = Volley.newRequestQueue(this);

        txt_name = (EditText) findViewById(R.id.txt_name);
        txt_username = (EditText) findViewById(R.id.txt_username);
        txt_password = (EditText) findViewById(R.id.txt_password);
        txt_mobile = (EditText) findViewById(R.id.txt_mobile);
        txt_email = (EditText) findViewById(R.id.txt_email);
        txt_city = (EditText) findViewById(R.id.txt_city);
        txt_address = (EditText) findViewById(R.id.txt_address);
        btn_register = (Button) findViewById(R.id.btn_register);
        btn_back_to_login = (Button) findViewById(R.id.btn_back_to_login);

        btn_register.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                btn_register.setEnabled(false);
                JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(
                        Request.Method.GET,
                        getRegisterUrl(),
                        null,
                        new Response.Listener<JSONObject>() {
                            @Override
                            public void onResponse(JSONObject response) {
                                try {
                                    if (response.getBoolean("status") == true) {
                                        int u_id = response.getJSONObject("data").getInt("u_id");

                                        editor.putInt("u_id", u_id);
                                        editor.commit();

                                        showToast(response.getJSONArray("smsg").getString(0));

                                        Intent i = new Intent(RegisterActivity.this, MainActivity.class);
                                        startActivity(i);
                                    } else {
                                        showToast(response.getJSONArray("emsg").getString(0));
                                        btn_register.setEnabled(true);
                                    }
                                } catch (Exception e) {
                                    showToast("Failed to Process Request");
                                    btn_register.setEnabled(true);
                                }
                            }
                        },
                        new Response.ErrorListener() {
                            @Override
                            public void onErrorResponse(VolleyError error) {
                                Log.e("TIMS", "onErrorResponse: " + error.toString());
                                showToast("Failed to Process Request");
                                btn_register.setEnabled(true);
                            }
                        }
                );
                requestQueue.add(jsonObjectRequest);
            }
        });

        btn_back_to_login.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                startActivity(new Intent(RegisterActivity.this, LoginActivity.class));
            }
        });
    }

    private String getRegisterUrl() {
        String name  = txt_name.getText().toString().trim();
        String username = txt_username.getText().toString().trim();
        String password = txt_password.getText().toString().trim();
        String mobile = txt_mobile.getText().toString().trim();
        String email= txt_email.getText().toString().trim();
        String city = txt_city.getText().toString().trim();
        String address = txt_address.getText().toString().trim();

        Uri registerUri = Uri.parse(getString(R.string.API_ENDPOINT) + "users.php")
                .buildUpon()
                .appendQueryParameter("register", "1")
                .appendQueryParameter("name", name)
                .appendQueryParameter("username", username)
                .appendQueryParameter("password", password)
                .appendQueryParameter("mobile", mobile)
                .appendQueryParameter("email", email)
                .appendQueryParameter("city", city)
                .appendQueryParameter("address", address)
                .build();

        return registerUri.toString();
    }

    private void showToast(String s) {
        Toast.makeText(getApplicationContext(), s, Toast.LENGTH_SHORT).show();
    }

    @Override
    public void onBackPressed() {
        moveTaskToBack(true);
    }
}